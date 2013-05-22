angular.module('services.localizedMessages', [])
.factory('localizedMessages', ['$interpolate', 'I18N.MESSAGES', function ($interpolate, i18nmessages) {
	var handleNotFound = function (msg, msgKey) {
		return msg || 'Mensaje no previsto (' + msgKey + ')';
	};

	return {
		get : function (msgKey, interpolateParams) {
			var msg =  i18nmessages[msgKey];
			if (msg) {
				return $interpolate(msg)(interpolateParams);
			} else {
				return handleNotFound(msg, msgKey);
			}
		}
	};
}])

//Para poner cosas del objeto que viene junto al error, tengo que usar la convención de ng. Ej: {{id}}
.constant('I18N.MESSAGES', {
	'errors.route.changeError':				'Ha ocurrido un error al intentar cargar la página',
	'login.error.notAuthorized':			'No tiene el permiso necesario para acceder a esa sección',
	'login.error.notAuthenticated':			'Debe ingresar al sistema para acceder a esa sección',

	'crud.socios.save.success':				'Se ha guardado correctamente el socio',
	'crud.socios.save.error':				'Ha ocurrido un error al intentar guardar el socio',
	'crud.socios.remove.success':			'Se ha eliminado correctamente el socio',
	'crud.socios.remove.error':				'Ha ocurrido un error al intentar eliminar el socio',
	'crud.socios.views.error':				'Ha ocurrido un error al intentar obtener el/los socios'
});