EasySocial.module('site/events/item', function($) {

var module = this;

EasySocial.require()
.library('history')
.done(function($) {

EasySocial.Controller('Events.Item', {
    defaultOptions: {
        id: null,
        title: null,

        // Filter item
        "{filterItem}": "[data-filter-item]",
        "{createFilter}": "[data-create-filter]",
        "{editFilter}": "[data-edit-filter]",
        "{saveFilter}": "[data-hashtag-filter-save]",

        // Sections
        "{section}": "[data-section]",
        "{sectionLists}": "[data-section-lists]",        

        // Contents area
        '{wrapper}': '[data-wrapper]',
        "{contents}": "[data-contents]"
    }
}, function(self, opts) { return {

    init: function() {
        opts.id = self.element.data('id');

        var activeFilter = self.filterItem('.active');
        opts.filter = activeFilter.data('filter-item');
        opts.filterId = activeFilter.data('id');
    },

    removeActive: function() {
        self.filterItem().removeClass('active');
        self.createFilter().removeClass('active');
    },

    getStream: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/events/getStream', {
            "filter": opts.filter,
            "filterId": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.setContents(filterItem, contents);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        }).always(function() {
            filterItem.removeClass('is-loading');
        });
    },

    getInfo: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/events/getInfo', {
            "step": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.setContents(filterItem, contents);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        }).always(function() {
            filterItem.removeClass('is-loading');
        });
    },

    getAppContents: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/events/getAppContents', {
            "appId": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.setContents(filterItem, contents);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        }).always(function() {
            filterItem.removeClass('is-loading');
        });
    },

    setContents: function(filterItem, contents) {
        // Remove loading indicators
        filterItem.removeClass('is-loading');
        self.wrapper().removeClass('is-loading');

        // Update the contents
        self.contents().html(contents);
    },

    setActiveFilter: function(filterItem) {
        // Toggle active state
        self.createFilter().removeClass('active');
        self.filterItem().removeClass('active');
        filterItem.addClass('active');

        var activeFilter = filterItem;
        opts.filter = filterItem.data('filter-item');
        opts.filterId = filterItem.data('id');

        // Update the url
        var anchor = filterItem.find('a');
        anchor.attr('title', opts.title);
        anchor.route();

        // Set loading class
        filterItem.addClass('is-loading');
        self.contents().empty();
        self.wrapper().addClass('is-loading');
    },

    addCustomFilter: function(feed) {
        var sectionContent = self.section('[data-type="custom-filters"]').find(self.sectionLists.selector);

        if (!$.trim(sectionContent.html()).length) {
            var emptyList = self.section('[data-type="custom-filters"]').find('[data-filter-empty]');
            emptyList.hide();
        }

        feed.appendTo(sectionContent);        
    },    

    "{createFilter} click": function(createFilter, event) {
        event.preventDefault();
        event.stopPropagation();

        createFilter.attr('title', opts.title);
        createFilter.route();

        // Remove active classes
        self.removeActive();
        createFilter.addClass('active');
        
        self.contents().empty();
        self.wrapper().addClass('is-loading');

        EasySocial.ajax('site/views/stream/getFilterForm', {
            "type": "event",
            "uid": opts.id
        }).done(function(contents) {
            self.contents().html(contents);
        }).always(function() {
            self.wrapper().removeClass('is-loading');
        });
    },

    "{editFilter} click": function(editFilter, event) {
        event.preventDefault();
        event.stopPropagation();

        editFilter.attr('title', opts.title);
        editFilter.route();

        // Filter attributes
        var id = editFilter.data('id');
        var type = editFilter.data('type');

        editFilter.addClass('is-loading');
        self.contents().empty();
        self.wrapper().addClass('is-loading');

        EasySocial.ajax('site/views/stream/getFilterForm', {
            "id": id,
            "uid": opts.id,
            "type": type
        }).done(function(contents){
            self.setContents(editFilter, contents);
        }).fail(function(messageObj){
            return messageObj;
        });
    },

    "{filterItem} click": function(filterItem, event) {
        event.preventDefault();
        event.stopPropagation();

        // Set the active filter
        self.setActiveFilter(filterItem);

        // Trigger so that other scripts can perform other stuffs if needed
        $('body').trigger('beforeUpdatingContents');

        if (opts.filter == 'info') {
            self.getInfo(filterItem);
        }

        if (opts.filter == 'feeds' || opts.filter == 'filters') {
            self.getStream(filterItem);
        }

        if (opts.filter == 'apps') {
            self.getAppContents(filterItem);
        }

        $('body').trigger('afterUpdatingContents');

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');
    },

    "{saveFilter} click": function(el) {
        var hashtag = el.data('tag');
        var uid = el.data('uid');

        EasySocial.dialog({
            content: EasySocial.ajax('site/views/stream/confirmSaveFilter', {"tag" : hashtag}),
            bindings: {
                "{saveButton} click": function() {
                    this.inputWarning().hide();

                    filterName = this.inputTitle().val();

                    if (filterName == '') {
                        this.inputWarning().show();
                        return;
                    }

                    EasySocial.ajax('site/controllers/stream/addFilter', {
                        "title": filterName,
                        "tag": hashtag,
                        "uid": uid,
                        "type": 'event'
                    }).done(function(html, msg) {
                        var item = $.buildHTML(html);

                        // Add a new custom filter to the sidebar
                        self.addCustomFilter(item);

                        // Show message
                        EasySocial.dialog(msg);

                        // Close the dialog automatically
                        setTimeout(function() {
                            EasySocial.dialog().close();
                        }, 2000);
                    });
                }
            }
        });
    }    

}});

    module.resolve();
});

});
