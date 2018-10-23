EasySocial.module('site/manage/clusters', function($) {

	var module 	= this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller('Clusters', {

			defaultOptions: {

				// Filter
				"{filterItem}": "[data-filter-item]",

				// Content area.
				"{wrapper}": "[data-wrapper]",
				"{contents}": "[data-contents]",

				// Result
				"{items}": "[data-items]",
				"{item}": "[data-item]",
				"{pagination}": "[data-pagination]",

				// Actions
				"{approve}": "[data-approve]",
				"{reject}": "[data-reject]",

				// Counters
				"{counters}": "[data-counter]"
			}
		}, function(self, opts) { return {

			init: function() {
				
			},

			updateCounter: function() {
				EasySocial.ajax('site/controllers/manage/getClusterCounters')
					.done(function(counters) {

						self.filterItem('[data-type="event"]')
							.find(self.counters.selector)
							.html(counters['event']);

						self.filterItem('[data-type="group"]')
							.find(self.counters.selector)
							.html(counters['group']);

						self.filterItem('[data-type="page"]')
							.find(self.counters.selector)
							.html(counters['page']);
					});
			},

			removeItem: function(id) {
				// Remove item from the list.
				var item = self.item('[data-id="' + id + '"]');

				item.remove();

				if (self.item().length <= 0) {
					self.items().addClass('is-empty');
					self.pagination().remove();
				}

				// Update the counter for the list items.
				self.updateCounter();
			},

			// Update the content on the items list.
			updateContents: function(html) {
				self.contents().html(html);

				$('body').trigger('afterUpdatingContents', [html]);
			},

			setActiveFilter: function(item) {

				// Remove all active classes
				self.filterItem().removeClass('active');

				// Add active class on itself
				item.addClass('active');
			},

			"{filterItem} click" : function(filterItem, event) {

				// Stop event from bubbling up
				event.preventDefault();
				event.stopPropagation();

				var type = filterItem.data('type');

				// Remove all active state on the filter links.
				self.setActiveFilter(filterItem);

				// Set the browsers attributes
				var anchor = filterItem.find('> a');
				anchor.route();

				// Add loading indicator
				filterItem.addClass('is-loading');

				var options = {
								"filter": type
							};


				self.wrapper().addClass('is-loading');
				self.contents().empty();

				EasySocial.ajax("site/controllers/manage/filterCluster", options)
					.done(function(html){

						self.updateContents(html);

						// trigger sidebar toggle for responsive view.
						self.trigger('onEasySocialFilterClick');

					}).always(function(){

						self.wrapper().removeClass('is-loading');
						filterItem.removeClass("is-loading");
					});
			},

			// Approve
			"{approve} click" : function(link, event) {
				// Get the cluster id
				var clusterId = link.closest(self.item.selector).data('id');
				var clusterType = link.closest(self.item.selector).data('type');

				EasySocial.dialog({
					content	: EasySocial.ajax("site/views/manage/confirmClusterApprove" , {"clusterId" : clusterId, "clusterType" : clusterType}),
					selectors: {
						"{sendMail}": "[data-send-email]",
						"{approveButton}": "[data-approve-button]",
						"{cancelButton}": "[data-cancel-button]"
					},
					bindings : {
						"{approveButton} click" : function() {

							var sendMail = this.sendMail().is(':checked') ? 1 : 0;

							EasySocial.ajax('site/controllers/manage/approveCluster', {
								"clusterId": clusterId,
								"clusterType": clusterType,
								"sendMail": sendMail
							})
							.done(function() {
								self.removeItem(clusterId);
								EasySocial.dialog().close();
							});
						},

						"{cancelButton} click": function() {
							EasySocial.dialog().close();
						}
					}
				});

			},

			// Reject
			"{reject} click" : function(link, event) {

				// Get the cluster id
				var clusterId = link.closest(self.item.selector).data('id');
				var clusterType = link.closest(self.item.selector).data('type');

				EasySocial.dialog({
					content: EasySocial.ajax("site/views/manage/confirmClusterReject" , {"clusterId" : clusterId, "clusterType": clusterType}),
					selectors: {
						"{rejectMessage}": "[data-reject-message]",
						"{rejectButton}": "[data-reject-button]",
						"{cancelButton}": "[data-cancel-button]",
						"{sendMail}": "[data-send-email]",
						"{deleteCluster}": "[data-delete-cluster]"
					},
					bindings : {
						"{rejectButton} click" : function() {
							var rejectMessage = this.rejectMessage().val();
							var sendMail = this.sendMail().is(':checked') ? 1 : 0;
							var deleteCluster = this.deleteCluster().is(':checked') ? 1 : 0;

							EasySocial.ajax('site/controllers/manage/rejectCluster', {
								"clusterId": clusterId,
								"clusterType": clusterType,
								"rejectMessage": rejectMessage,
								"sendMail": sendMail,
								"deleteCluster": deleteCluster
							})
							.done(function() {
								self.removeItem(clusterId);
								EasySocial.dialog().close();
							});
						},

						"{cancelButton} click": function() {
							EasySocial.dialog().close();
						}
					}
				});
			}
		}});

		module.resolve();
	});
});
