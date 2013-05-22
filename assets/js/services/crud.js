angular.module('crud', []).factory('crud', ['$routeParams' , '$resource', 'notificationsDriver', function($routeParams, $resource, notificationsDriver){
    return {
        apiUrl  :   'api/v1/',
        defaultMethods: {update: {method:'PUT'}},
        crudify :   function(resource, methods) {
            methods = angular.extend(this.defaultMethods, methods);
            var resourceFactory = $resource(this.apiUrl + resource + '/:id', {id: '@id'}, methods);
            return {
                resourceName		: resource,
                $resource			: $resource,
                $routeParams		: $routeParams,
				notificationsDriver	: notificationsDriver,
                resourceFactory		: resourceFactory,
                resource			: new resourceFactory(),
                create				: function() {
                    return this.resource.$save(jQuery.proxy(function(data){
						this.notificationsDriver.pushForCurrentRoute('crud.' + this.resourceName + '.create.success', 'success', {id : this.resource.id});
                    }, this));
                },
                read            : function() {
                    return this.resource.$get({id: this.$routeParams.id});
                },
                update          : function() {
                    return this.resource.$update(jQuery.proxy(function(data){
						this.notificationsDriver.pushForCurrentRoute('crud.' + this.resourceName + '.update.success', 'success', {id : this.resource.id});
                    }, this));
                },
                delete          : function(id) {
                    return this.resource.$delete({id:id}, jQuery.proxy(function(data){
						this.notificationsDriver.pushForCurrentRoute('crud.' + this.resourceName + '.remove.success', 'success', {id : this.resource.id});
                        if (this.filters != undefined) {
                            if (this.filters.reloadFlag != undefined) {
                                this.filters.reloadFlag = Math.random();
                            }
                        }
                    }, this));
                }
            }
        }
    }
}]);