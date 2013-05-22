angular.module('services.exceptionHandler', ['services.notificationsDriver'])
.factory('exceptionHandlerFactory', ['$injector', function($injector) {
	return function($delegate) {
		return function (exception, cause) {
			var notificationsDriver = $injector.get('notificationsDriver');
			$delegate(exception, cause);

			// Push a notification error
			notificationsDriver.pushForNextRoute('error.fatal', 'error', {}, {
				exception:exception,
				cause:cause
			});
		};
	};
}])
.config(['$provide', function($provide) {
	$provide.decorator('$exceptionHandler', ['$delegate', 'exceptionHandlerFactory', function ($delegate, exceptionHandlerFactory) {
		return exceptionHandlerFactory($delegate);
	}]);
}]);
