angular.module('dataResource', []).factory('dataResource', ['$http', '$q', function ($http, $q) {
	function DataResourceFactory(collectionName, prefix, extension) {
		extension = extension ? extension : {};
		var url = (prefix ? prefix : '') + collectionName + '/';
		var defaultParams = {};

		var thenFactoryMethod = function (httpPromise, successcb, errorcb, isArray) {
			var scb = successcb || angular.noop;
			var ecb = errorcb || angular.noop;

			return httpPromise.then(function (response) {
				var result;
				if (isArray) {
					result = [];
					for (var i = 0; i < response.data.object.length; i++) {
						result.push(new Resource(response.data.object[i]));
					}
					response.data.object = result;
				} else {
					//Todo: este if es cualquiera. La primera parte no sirve, o hay que buscarle una mejor manera. Ya hay algo que maneja los 404?
					//MongoLab has rather peculiar way of reporting not-found items, I would expect 404 HTTP response status...
					if (response.data === " null "){
						return $q.reject({
							code: 'resource.notfound',
							collection: collectionName
						});
					} else {
						response.data.object = new Resource(response.data.object);
					}
				}
				scb(response.data, response.status, response.headers, response.config);
				return response.data.object;
			}, function (response) {
				ecb(response.data, response.status, response.headers, response.config);
				return undefined;
			});
		};

		var Resource = function (data) {
			//angular.extend(this, extension);
			angular.extend(this, data);
		};

		Resource.all = function (cb, errorcb) {
			return Resource.query({}, cb, errorcb);
		};

		Resource.query = function (queryJson, successcb, errorcb) {
			var params = angular.isObject(queryJson) ? {query: JSON.stringify(queryJson)} : {};
			var httpPromise = $http.get(url + 'get/', {params: angular.extend({}, defaultParams, params)});
			return thenFactoryMethod(httpPromise, successcb, errorcb, true);
		};

		Resource.list = function (successcb, errorcb) {
			var httpPromise = $http.get(url + 'autocomplete/list');
			return thenFactoryMethod(httpPromise, successcb, errorcb, true);
		};

		Resource.getById = function (id, successcb, errorcb) {
			var httpPromise = $http.get(url + 'get/' + id, {params:defaultParams});
			return thenFactoryMethod(httpPromise, successcb, errorcb);
		};

		Resource.getByIds = function (ids, successcb, errorcb) {
			var qin = [];
			angular.forEach(ids, function (id) {
				qin.push({id: id});
			});
			return Resource.query({id:{$in:qin}}, successcb, errorcb);
		};

		//instance methods

		Resource.prototype.$id = function () {
			if (this.id) {
				return this.id;
			}
			return false;
		};

		Resource.prototype.$save = function (successcb, errorcb) {
			var httpPromise = $http.post(url + 'add/', this, {params:defaultParams});
			return thenFactoryMethod(httpPromise, successcb, errorcb);
		};

		Resource.prototype.$update = function (successcb, errorcb) {
			//var httpPromise = $http.put(url + 'edit/' + this.$id(), angular.extend({}, this, {_id:undefined}), {params:defaultParams});
			var httpPromise = $http.post(url + 'edit/' + this.$id(), angular.extend({}, this, {_id:undefined}), {params:defaultParams});
			return thenFactoryMethod(httpPromise, successcb, errorcb);
		};

		Resource.prototype.$remove = function (successcb, errorcb) {
			//var httpPromise = $http['delete'](url + 'delete/' + this.$id(), {params:defaultParams});
			var httpPromise = $http.post(url + 'delete/' + this.$id(), {params:defaultParams});
			return thenFactoryMethod(httpPromise, successcb, errorcb);
		};

		Resource.prototype.$saveOrUpdate = function (savecb, updatecb, errorSavecb, errorUpdatecb) {
			if (this.$id()) {
				return this.$update(updatecb, errorUpdatecb);
			} else {
				return this.$save(savecb, errorSavecb);
			}
		};

		return Resource;
	}
	return DataResourceFactory;
}]);
