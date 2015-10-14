document.observe('dom:loaded', function() {
    
    var mmCmsBlockAttrSelector = '.megamenu-cms-block ';
    var mmTypeAttrSelector = '.megamenu-type';
    var mmTypeId = 'megamenu';
    
    function changeInputWrapperVisibility(inputSelector, isShow) {
        $$(inputSelector).each(function(element){
            if (isShow) {
                element.up(1).show();
            } else {
                element.up(1).hide();
            }
        });
    }
    
    function updateDependentFields(event, element) {
        var isShow = $F(element) == mmTypeId;
        changeInputWrapperVisibility(mmCmsBlockAttrSelector, isShow);
    }
    
    function initialize() {
        $$(mmTypeAttrSelector).each(function(element) {
            updateDependentFields(null, element);
            element.on('change', updateDependentFields);
        });
    }

    initialize();
    Ajax.Responders.register({
        onComplete: function() {
            initialize();
        }
    });    

});

