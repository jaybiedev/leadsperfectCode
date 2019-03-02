var Helper = {
	
	BaseUrl : window.location.origin,
    BaseTemplateUrl : '/assets/js/leads/directives/templates',
    BaseApiUrl : window.location.origin + '/api/v1/',
    CommonTemplateUrl : '/assets/js/angular/directives/templates',

    getSecurityUrl : function() {
        return this.BaseApiUrl + '/Security';
    },

    serializeData : function ( data ) {
        // If this is not an object, defer to native stringification.
        if ( ! angular.isObject( data ) ) {
            return( ( data == null ) ? "" : data.toString() );
        }
        var buffer = [];
        // Serialize each key in the object.
        for ( var name in data ) {
            if ( ! data.hasOwnProperty( name ) ) {
                continue;
            }
            var value = data[ name ];
            buffer.push(
                encodeURIComponent( name ) +
                "=" +
                encodeURIComponent( ( value == null ) ? "" : value )
            );
        }
        // Serialize the buffer and clean it up for transportation.
        var source = buffer
            .join( "&" )
            .replace( /%20/g, "+" )
        ;
        return( source );
    }
}