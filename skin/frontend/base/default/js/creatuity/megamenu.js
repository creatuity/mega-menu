$j = jQuery.noConflict();

window.Creatuity = window.Creatuity || {}

window.Creatuity.MegaMenu = Class.create({
    
    // Config
    forceWorkOnClick: false,
    mobileModeSize: 768,
    breadcrumbsLinksToCategory: null,
    isActivationDisabled: false,
    
    
    // Selectors & classes
    menuWrapper : '#megamenu-navigation',
    itemSelector: '.item',
    breadcrumbSelector: '.breadcumb-item',
    megamenuWrapperSelector: '.megamenu-panels',
    menuWrapperSelector: '.menu-panels',
    generalContainerSelector: '.container',
    activeElementClass: "active",
    backButton: ".back-button",
    mobileModeSelector: "mobile-mode",
    ajaxLoader: ".ajax-loader",
    viewAll: ".view-all",
    mobileMode: null,
    menuTypeContainersToDeactivate: [],
    currentElementClass: 'current',
    
    
    // Container types
    megaMenuContainerType: "megamenu",
    menuContainerType: "menu",
    
    // Menu items data attributes
    categoryIdAttribute: 'data-cat-id',
    itemContainerAttribute: 'data-child',
    
    // Menu Elements
    elements: [], // <a> elements
    menuItems: [], // 
    menuItemsByCategories: [], // MegaMenu Items indexed by their data-cat-id attribute
    activeMenuItems: [],
    allItems: [],
    currentCategory: null,
    
    // Menu Containers 
    megamenuContainers: [],
    menuContainers: [],
    activeMegaMenuContainers: [],
    activeMenuContainers: [],
    
    // Events 
    turnOnEvent: null,
    turnOffEvent: null,
    
    initialize: function() {
        if(!$j(this.menuWrapper).length) {
            throw "MegaMenu HTML structure was not loaded";
        } else {
            this.startMenuInitialization();
        }
    },
    
    startMenuInitialization: function() {
        this.forceWorkOnClick = CreatuityMegaMenuConfig.force_work_on_click;
        this.mobileModeSize = CreatuityMegaMenuConfig.mobile_mode_size;
        this.breadcrumbsLinksToCategory = CreatuityMegaMenuConfig.breadcrumbs_links_to_category;
        this.currentCategory = CreatuityMegaMenuConfig.current_category_id;
        this.initializeBehaviourEvents();
        this.elements = $j(this.menuWrapper).find(this.itemSelector + '[' + this.itemContainerAttribute + ']');
        this.populateMenuItems(this.elements);
        this.setMenuMode();
        this.observeWindow();
        this.toggleViewAllElements();
        this.markCurrentMenuItem();
    },
    
    markCurrentMenuItem: function() {
        if (this.currentCategory) {
            var menu = this;
            $j(this.menuWrapper+" [data-cat-id='"+this.currentCategory+"']").each(function(index, element){
                $j(element).parent().addClass(menu.currentElementClass);
            }).bind(menu);
        }
    },
    
    // Determine the "click" event naming 
    obtainOnClickEvent: function(){
        var event = navigator.userAgent.match(/iphone|ipad/gi)
                ? "touchstart" 
                : "click";
        return event;
    },
    
    // Initalize action events
    initializeBehaviourEvents: function() {
        if(this.forceWorkOnClick || this.isReadyForMobileMode(false)) {
            this.turnOffEvent = this.turnOnEvent = this.obtainOnClickEvent();
        } else {
            this.turnOnEvent = 'mouseenter';
            this.turnOffEvent = 'mouseleave';
        }
    },
    
    setMenuMode: function(){
        if(this.isMobileSize()){
            this.enableMobileMode();
        } else {
            this.disableMobileMode();
        }
    },
    
    observeWindow: function(){
        var menu = this;
        $j(window).on('resize', function(){
            if(menu.mobileMode != menu.isMobileSize()){
                menu.setMenuMode();
                menu.toggleViewAllElements();
            }
        }).bind(menu);
        $j(window).on(this.obtainOnClickEvent(), function(e) {
            if(!$j(menu.menuWrapper).has(e.target).length && menu.activeMenuItems.length > 0 ) {
                menu.deactivateMenuItems();
            }
        }).bind(menu)
    },
    
    enableMobileMode: function() {  
        this.deactivateMenuItems();
        this.menuItemsByCategories.each(function(element){
            element.reObserveMenuItem();
        });
        $j(this.menuWrapper).addClass(this.mobileModeSelector);
        this.mobileMode = true;
    },
    
    disableMobileMode: function() {
        this.deactivateMenuItems();
        this.menuItemsByCategories.each(function(element){
            element.reObserveMenuItem();
        });
        $j(this.menuWrapper).removeClass(this.mobileModeSelector);
        this.mobileMode = false;
    },
    
    // Checks if the Window width is smaller than defined mobile mode
    isMobileSize: function(){
        return $j(window).width() < this.mobileModeSize;
    },
    
    // Determines if the mobile mode should be enable/disabled
    isReadyForMobileMode: function() {
        if(this.isMobileSize() || Modernizr.touch){
            return true;
        } else {
            return false;
        }
    },
    
    // Populates menu items based on default/particular stack
    populateMenuItems: function(stackToOperate) {
        var menu = this;
        var itemsToProcessItemData = [];
        
        // TODO 
        stackToOperate.each(function(){
            var elementCategoryId = $j(this).attr(menu.categoryIdAttribute);
            if(menu.menuItemsByCategories[elementCategoryId] === undefined) {
                menu.menuItemsByCategories[elementCategoryId] = new Creatuity.MegaMenu.Item($j(this), menu, elementCategoryId);
                itemsToProcessItemData.push(menu.menuItemsByCategories[elementCategoryId]);
            }
        }); 
        
        for(var i = 0; i < itemsToProcessItemData.length; i++) {
            itemsToProcessItemData[i].processItemData();
        }
    },
    
    
    
    activateMenuItem: function(menuItem) {
            var activeMenuItems =  this.activeMenuItems;   
            var parentIndexInActiveMenuItems = activeMenuItems.indexOf(this.menuItemsByCategories[menuItem.parentMenuItem]);

            if(parentIndexInActiveMenuItems == -1) {
                this.deactivateMenuItems();

            } else {
                this.deactivateMenuItems(parentIndexInActiveMenuItems + 1);
            }

            activeMenuItems.push(menuItem);
            menuItem.setIsActive();
    },
    
    deactivateMenuItem: function(menuItem) {  
        this.deactivateMenuItems(this.activeMenuItems.indexOf(menuItem));
    },
    
    deactivateMenuItems: function(index) {
//        if(!this.isActivationDisabled) {
            var startingIndex = index ? index : 0;
            if(this.activeMenuItems.length > 0) {
                var removedElements = this.activeMenuItems.splice(startingIndex, this.activeMenuItems.length - startingIndex);
                for(var i = 0; i < removedElements.length; i++){
                    removedElements[i].setIsNotActive();
                }
            }
//        }
    },
    
    activatePreviuosMegaMenuContainer: function(){
        if(this.activeMegaMenuContainers[this.activeMegaMenuContainers.length - 2]){
            this.deactivateMenuItem(this.activeMegaMenuContainers[this.activeMegaMenuContainers.length - 1].parentItem);
            this.activateMenuItem(this.activeMegaMenuContainers[this.activeMegaMenuContainers.length - 1].parentItem);
        }
    },
    
    upgradeMenu: function(elements, currentElement) {
        this.insertLoadedMenus(elements);
        this.activateMenuItem(this.menuItemsByCategories[currentElement]);
        this.markCurrentMenuItem();
    },
    
    insertLoadedMenus: function(elements) {
        var menu = this;
        var stackToOperate;
        for(var i = 0; i < elements.length; i++ ) {
            if(!this.isContainerLoaded(elements[i].category_id)) {
                if(elements[i].type == menu.menuContainerType) {
                    stackToOperate = $j(elements[i].html).appendTo(menu.menuWrapperSelector);
                } else if (elements[i].type == menu.megaMenuContainerType) {
                    stackToOperate = $j(elements[i].html).appendTo(menu.megamenuWrapperSelector);
                }
                
                var items = $j(stackToOperate).find(menu.itemSelector + '[' + menu.itemContainerAttribute + ']');
                if(items.length > 0 ) {
                    menu.populateMenuItems(items);
                }
            }
        }
    },
    
    performDelayedDeactivation: function() {
        if(this.menuTypeContainersToDeactivate.length > 0) {
            this.deactivateMenuItem(this.menuTypeContainersToDeactivate[0]);
            this.menuTypeContainersToDeactivate = [];
        }
    },
    
    toggleViewAllElements: function() {
        var that = this;
        $j(this.viewAll).each(function() {
            var item = $j(this).closest('li');
            if(that.isReadyForMobileMode() || that.forceWorkOnClick) {
                item.show();
            } else {
                item.hide();
            }
        }).bind(that)
    },
    
    initializeSlideshow: function() {
        $j('.megamenu-slideshow-container .slideshow').cycle({
            slides: '> li',
            pager: '~ .slideshow-pager',
            pagerTemplate: '<span class="pager-box"></span>',
            speed: 600,
            pauseOnHover: true,
            swipe: true,
            prev: '~ .slideshow-prev',
            next: '~ .slideshow-next',
            fx: 'fade'
        }); 
    },
    
    isContainerLoaded: function(containerId) {
        return $j('div' + '[' + this.categoryIdAttribute + '=' + containerId + ']').length;
    },
    
    disableItemActivation: function() {
        $j(this.menuWrapper).addClass('wait');
        $j(this.menuWrapper).bind('click mousedown dblclick',function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
        });
    },
    
    enableItemActivation: function() {
        $j(this.menuWrapper).removeClass('wait');
      $j(this.menuWrapper).unbind();
    }
    
});

window.Creatuity.MegaMenu.Item = Class.create({
    menu: null,
    categoryId: null,
    itemContainer: null,
    itemExecutor: null,
    isActive: null,
    parentMenuItem: null,
    parentLiItem: null,
    activationEvent: null,
    activationDelay: null,
    enterEvent: null, 
    leaveEvent: null, 
    delayedMenuDeactivation: null,
    
    
    initialize: function(element, menu, elementCategoryId){
        this.isActive = false;
        this.itemExecutor = element;
        this.parentLiItem = element.closest('li');
        this.menu = menu;
        this.categoryId = elementCategoryId;
        this.enterEvent = this.onItemActivationEvent.bind(this);
        this.leaveEvent = this.onItemDeactivationEvent.bind(this);
    },
    
    processItemData: function(){
        if(this.isParentMenuItem){
            var containerType = this.itemExecutor.attr(this.menu.itemContainerAttribute);
            var containerId = this.itemExecutor.attr(this.menu.categoryIdAttribute);
            this.initializeItemContainer(containerType,containerId); 
        }
        this.setParentMenuItem();
        this.setActivationEvent();
        this.observeItem();
    },
    
    initializeItemContainer: function(containerType,containerId){
        switch (containerType) {
            case this.menu.megaMenuContainerType : 
                this.itemContainer = new window.Creatuity.MegaMenu.MegaMenuContainer(this.menu, this, containerId);
                this.menu.megamenuContainers.push(this.itemContainer);
            break;
            case this.menu.menuContainerType : 
                this.itemContainer = new window.Creatuity.MegaMenu.MenuContainer(this.menu, this, containerId);
                this.menu.menuContainers.push(this.itemContainer);
            break;    
        }  
    },
    
    setParentMenuItem: function() {        
        var containerId = this.parentLiItem.closest(this.menu.generalContainerSelector).attr(this.menu.categoryIdAttribute);
        if(containerId) {
            this.parentMenuItem = containerId;
        }
    },
    
    setActivationEvent: function() {        
        if(this.menu.isReadyForMobileMode(false) 
            || (this.parentMenuItem 
                && this.itemContainer 
                && this.itemContainer.containerType === this.menu.megaMenuContainerType)) 
        {
            this.activationEvent = this.menu.obtainOnClickEvent();
            this.activationDelay = 0;
        } else {
            this.activationEvent = this.menu.turnOnEvent;
            this.activationDelay = this.menu.activationDelay;
        }
    },
    
    observeItem: function(){
        $j(this.parentLiItem).on(this.menu.turnOffEvent, this.leaveEvent);
        $j(this.itemExecutor).on(this.activationEvent, this.enterEvent);   
    },
    
    // Perform item activation
    onItemActivationEvent: function(e) {
        e.preventDefault();
        if(!this.menu.isActivationDisabled) {
            var itemCategoryId = $j(e.currentTarget).children().closest(this.menu.itemSelector).attr(this.menu.categoryIdAttribute);
            var menuItem = this.menu.menuItemsByCategories[itemCategoryId];

            if(!menuItem.isActive) {
                if(this.canShowContainer()){
                    this.menu.activateMenuItem(menuItem);
                } else {
                    this.itemContainer.loadContainer();
                }
            } else {
                if(this.menu.forceWorkOnClick || this.menu.isReadyForMobileMode()) {
                    this.menu.deactivateMenuItem(menuItem);
                }
            }
        }
        
    },    
    
    onItemDeactivationEvent: function(){
        if(!this.menu.isReadyForMobileMode() && !this.menu.forceWorkOnClick){
            // Deactivate Menu type item on mouseout
            if(this.itemContainer.containerType === this.menu.menuContainerType 
                && this.isActive 
                && this.menu.activeMenuItems[this.menu.activeMenuItems.length - 1].itemContainer.containerType === this.menu.menuContainerType)
            {

                this.delayedMenuDeactivation = setTimeout(this.delayMenuItemDeactivation.bind(this), 20);

                var eventObject = $j._data($j(this.itemContainer.container).get(0), 'events');
                var requiresAddingObserver = true;

                if (eventObject && eventObject.mouseover) {
                    for (var x = 0; x < eventObject.mouseover.length; x++) {
                          if(eventObject.mouseover[x].data && eventObject.mouseover[x].data.megamenu_mouseover) {
                              requiresAddingObserver = false;
                              break;
                          }
                    }
                } 

                if(requiresAddingObserver) {
                    this.observeItemContainer();
                }
            }
        }
    },
    
    observeItemContainer: function(){
        $j(this.itemContainer.container).on(this.menu.turnOnEvent, {'megamenu_mouseover': true}, this.clearItemDeactivationTimeout.bind(this));
    },

    delayMenuItemDeactivation: function() {
        this.menu.deactivateMenuItem(this);
    },
 
    clearItemDeactivationTimeout: function() {
        clearTimeout(this.delayedMenuDeactivation);
    },
    
    reObserveMenuItem: function() {
        $j(this.itemExecutor).off(this.activationEvent, this.enterEvent);
        $j(this.parentLiItem).off(this.menu.turnOffEvent, this.leaveEvent);
        this.setActivationEvent();
        this.observeItem(); 
    },
    
    hasContainer: function(){
       return this.itemContainer ? true : false;
    },
    
    canShowContainer: function() {
        return this.itemContainer.identifyContainer().length;
    },
    
    setIsActive: function() {
        this.isActive = true;
        this.parentLiItem.addClass(this.menu.activeElementClass);
        this.itemContainer.activateContainer();
    },
    
    setIsNotActive: function() {
        this.isActive = false;
        this.itemContainer.deactivateContainer();
        this.parentLiItem.removeClass(this.menu.activeElementClass);
    },
    
    isParentMenuItem: function(){
        return Boolean($j(this.itemExecutor).attr(this.menu.itemContainerAttribute));
    }
    
});

window.Creatuity.MegaMenu.containerAbstract = Class.create({
    menu: null,
    isActive: null,
    parentItem: null,
    childMenuItems: [],
    container: null,
    containerId: null,
    containerType: null,
    
    
    initialize: function(menu, parentMenuItem, containerId){
        this.menu = menu;
        this.parentItem = parentMenuItem;
        this.containerId = containerId;
        this.setContainerType();
    },
    
    identifyContainer: function(){
        return $j('div' + '[' + this.menu.categoryIdAttribute + '=' + this.containerId + ']');
    },
    
    loadContainer: function() {
        var url = CreatuityMegaMenuConfig.controller_path;
        var params = {};
        params.json = true;
        params.megamenu_cache_key = CreatuityMegaMenuConfig.megamenu_cache_key;
        
        if (CreatuityMegaMenuConfig.all_categories_mode) {
            params.all_categories_mode = true;
        } else {
            params.category_id = this.containerId;
        }

        new Ajax.Request(url, {
            parameters: params,
            method: 'get',
            
            onCreate: function() {
              $j(this.menu.ajaxLoader).show();
              this.menu.isActivationDisabled = true;
              this.menu.disableItemActivation();
            }.bind(this),
            
            onComplete : function() {
               $j(this.menu.ajaxLoader).hide();
               this.menu.isActivationDisabled = false;
               this.menu.enableItemActivation();
            }.bind(this),
            
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(response.status && response.results) {
                    this.onAjaxSuccess(response.results,this.containerId);
                } else {
                    this.onAjaxFailure(response.error_message);
                }
            }.bind(this),
            
            onFauilure : function(transport) {
                var response = transport.responseText.evalJSON();
                this.onAjaxFailure(response.errorMessage
                    ? response.errorMessage
                    : "Server error", this.containerId);
            }.bind(this)
        });
    },
    
    
    onAjaxSuccess : function(results, category_id) {
        this.menu.upgradeMenu(results, category_id );    
    },

    onAjaxFailure : function(message) {
        this.menu.deactivateMenuItem(this.menu.menuItemsByCategories[params.category_id]);
    }
});

window.Creatuity.MegaMenu.MegaMenuContainer = Class.create(Creatuity.MegaMenu.containerAbstract, {
    backButton: null,
    enterEvent: null,
    backButtonEnterEvent: null,
    isMovedToParentElement: null,
    breabcrumbs: null,
    breadcrumbItems: [],
    
    
    setContainerType: function() {
        this.containerType = this.menu.megaMenuContainerType;
        this.backButtonEnterEvent = this.onBackLinkClickAction.bind(this);
    },
    
    activateContainer: function(){
        if(this.menu.activeMegaMenuContainers.length > 0) {
            for(var i = 0; i < this.menu.activeMegaMenuContainers.length; i++) {
                this.menu.activeMegaMenuContainers[i].setIsNotActive();
            }
        }
        if(this.menu.activeMegaMenuContainers.indexOf(this) == -1 
            || this.menu.activeMegaMenuContainers.indexOf(this) != this.menu.activeMegaMenuContainers.length - 1) {
            this.menu.activeMegaMenuContainers.push(this); 
        }
        this.showContainer();
    },
    
    deactivateContainer: function() {
        if(this.menu.activeMegaMenuContainers.length > 0) {
            this.menu.activeMegaMenuContainers.splice(this.menu.activeMegaMenuContainers.indexOf(this),1);
            this.hideContainer();
        }
    },
    
    initializeBackLink: function() {
        var container = this.identifyContainer();
        var backButton = $j(container).find(this.menu.backButton);
        if(backButton.length > 0) {
            if(this.backButton == null) {
                this.backButton = backButton;
            }
            if(!this.parentItem.parentMenuItem) {
                $j(backButton).hide();
            } else {
                this.container.addClass('extra-p')
            }
            backButton.on(this.menu.obtainOnClickEvent(), this.backButtonEnterEvent);
        }
    },
    
    deinitializeBackLnk: function() {
        this.backButton.off(this.menu.obtainOnClickEvent(), this.enterEvent);
    },
    
    onBackLinkClickAction: function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.menu.activatePreviuosMegaMenuContainer();
    },
    
    showContainer: function(){
        this.container =  this.identifyContainer();

        if(this.menu.mobileMode){
            this.isMovedToParentElement = true;
            this.container.appendTo(this.parentItem.parentLiItem);
        }
        this.setIsActive();
        if(this.parentItem.parentMenuItem) {
            this.generateBreadcrumbs();
        }
        
    },
    
    
    
    hideContainer: function(){
        this.setIsNotActive();
        if(this.isMovedToParentElement){
            this.container.appendTo(this.menu.megamenuWrapperSelector);
        }
    },

    setIsActive: function() {
        this.isActive = true;
        this.initializeBackLink();
        this.container.addClass(this.menu.activeElementClass);
        this.menu.initializeSlideshow();
    },
        
    setIsNotActive: function(){
        this.isActive = false; 
        if(this.container) {
            this.deinitializeBackLnk();
            this.container.removeClass(this.menu.activeElementClass);
        }
    },
    
    generateBreadcrumbs: function() {
        if(!this.breabcrumbs) {
            this.breadcrumbItems = [];
            var html = '<div class="breadcrumbs"><ul>';
            for(var i = 0; i < this.menu.activeMegaMenuContainers.length; i++) {
                var item = '<a href="' + $j(this.menu.activeMegaMenuContainers[i].parentItem.itemExecutor[0]).attr('href') + '" class="breadcrumb-item" data-cat-id="' + this.menu.activeMegaMenuContainers[i].parentItem.categoryId + '">';
                item += $j(this.menu.activeMegaMenuContainers[i].parentItem.itemExecutor[0]).text() + '</a>';
                this.breadcrumbItems.push(this.menu.activeMegaMenuContainers[i].parentItem.categoryId);
                html += '<li>' + item + '</li>';
                if(i < this.menu.activeMegaMenuContainers.length - 1 ) {
                    html += '<li class="breadcrumb-separator"> > </li>';
                }
            }
            html += '</ul></div>';         
            this.breabcrumbs = html;
            this.container.prepend(html);
            if(!this.menu.breadcrumbsLinksToCategory) {
                this.setBreadcrumbsEvents();
            }
            
        }
    },
    
    setBreadcrumbsEvents: function() {
        var menu = this.menu;
        for(var i = 0; i < this.breadcrumbItems.length; i++) {
            var breadcumbSelector = $j(this.container).find('.breadcrumb-item' + '[' + this.menu.categoryIdAttribute + '="' + this.breadcrumbItems[i] + '"]');
            breadcumbSelector.on(menu.obtainOnClickEvent(), function(e){
                e.preventDefault();
                menu.activateMenuItem(menu.menuItemsByCategories[$j(this).attr(menu.categoryIdAttribute)]);
            }).bind(menu)
            
        }
    }
});

window.Creatuity.MegaMenu.MenuContainer = Class.create(Creatuity.MegaMenu.containerAbstract, {
    
    setContainerType: function() {
        this.containerType = this.menu.menuContainerType;
    },
    
    showContainer: function(){
        this.container =  this.identifyContainer();
        this.container.appendTo(this.parentItem.parentLiItem);
        if(!this.menu.isMobileSize()) {
            this.setContainerPosition();
        }
        this.setIsActive();
    },
    
    hideContainer: function(){
        this.setIsNotActive();
        if(this.container){
            this.container.appendTo(this.menu.menuWrapperSelector);
            if(!this.menu.isMobileSize()) {
                this.container.css({
                    'left': 0,
                    'right': 0
                });
            }
        }
    },
    
    setIsActive: function() {
        this.container.addClass(this.menu.activeElementClass);
        this.isActive = true;
        
    },
        
    setIsNotActive: function(){
        this.isActive = false; 
        if(this.container) {
            this.container.removeClass(this.menu.activeElementClass);
        }
    },
    
    activateContainer: function(){       
        if(this.menu.activeMenuContainers.indexOf(this) < 0) {
            this.menu.activeMenuContainers.push(this);
        }
        this.showContainer();        
        
    },
    
    deactivateContainer: function(){
        this.menu.activeMenuContainers.splice(this.menu.activeMenuContainers.indexOf(this),1);
        this.hideContainer();
    },
    
    setContainerPosition: function() {
        var container = this.identifyContainer();
        
        var windowWidth = $j(window).outerWidth();
        var parentMenuItemLeftOffset = this.parentItem.itemExecutor.offset().left;
        var parentMenuItemWidth = this.parentItem.itemExecutor.outerWidth();
        
        
        if(this.parentItem.parentMenuItem == null) {
            container.css({'top' : 52});
            if(parentMenuItemLeftOffset + this.container.outerWidth() > windowWidth) {
                container.css({
                    'right' : 0,
                    'left' : 'auto',
                    'min-width' : 220
                });
            } else {
                container.css({
                    'right' : 'auto',
                    'left' : 0,
                    'min-width' : 220
                });
            }
        } else {
            var containerWidth = container.width(); 
            if(parentMenuItemLeftOffset + parentMenuItemWidth + this.container.outerWidth() > windowWidth) {
                container.css({
                    'left' : - parentMenuItemWidth - 2,
                    'right' : 'auto',
                    'width' : parentMenuItemWidth
                     
                });
            } else {
                container.css({
                    'right' : - parentMenuItemWidth - 2,
                    'left' : 'auto',
                    'width' : parentMenuItemWidth
                });
            }
        }
    }
    
});

$j(document).ready(function(){
    var megamenu = new Creatuity.MegaMenu();
})