
var EventsApp = angular.module('EventsApp', []);


EventsApp.factory('FormData', function () {

    var types = [];

    event_types.forEach(function(type){
        types.push({name: type, value: 0});
    });


    var FormData = {
        types: types
    };

    return FormData;
});


EventsApp.factory('EventStorage', [ '$http', 'FormData', function ($http, FormData) {


    var Storage = {
        data: [],
        total: 0
    };

    Storage.count = function(){
        return this.data.length;
    };

    Storage.setTotal = function(val){
        this.total = val;
    };

    Storage.getTotal = function(){
        return this.total;
    };

    Storage.setEvents = function(events){
        this.data = events;
    };

    Storage.append = function(data){
        this.data = this.data.concat(data);
    };

    Storage.fetch = function(){

        var params = FormData;
        params.offset = 0;//Storage.count();

        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);

        $http({
            url     : events_url,
            data    : $.param(params),
            method  : 'POST',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
            .success(function(response) {
                //console.log(response);

                if (!response.success) {

                    console.log('error');
                } else {

                    Storage.setEvents(response.events);
                    Storage.setTotal(response.totalCount);
                }

                if(preloader){ preloader.remove(); }
            });
    };

    Storage.fetchMoreEvents = function(){

        var params = FormData;
        params.offset = Storage.count();

        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);

        $http({
            url     : events_url,
            data    : $.param(params),
            method  : 'POST',
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
        .success(function(response) {

            Storage.append(response.events);
            Storage.setTotal(response.totalCount);

            if(preloader){ preloader.remove(); }
        });

    };

    Storage.find = function(id){

        if(!id){
            return null;
        }

        for(var i=0; i<this.data.length; i++)
        {
            var event = this.data[i];
            if(event.id == id){
                return event;
            }
        }

        return null;
    };

    return Storage;
}]);

//        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);
//
//        $http({
//            url     : events_url,
//            data    : $.param({
//                currentCount: currentCount
//            }),
//            method  : 'POST',
//            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
//        })
//        .success(function(response) {
//            //console.log(response);
//
//            if (!response.success) {
//
//                console.log('error');
//            } else {
//
////                        this.append(response.events);
////                        this.setTotal(response.totalCount);
//
//                data = data.concat(response.events);
//                total = response.totalCount;
//            }
//
//            if(preloader){ preloader.remove(); }
//        });
//    }


//EventsApp.factory('EventRepository', ['$scope', '$http', 'EventStorage', function($scope, $http, EventStorage){

//    $scope.storage = EventStorage;

//    var service = {

//    };

//    return null;
//}]);



EventsApp.controller('FormCtrl', ['$scope', 'EventStorage', 'FormData', function ($scope, EventStorage, FormData) {

    $scope.formData = FormData;

    $scope.submit = function(){
        EventStorage.fetch();
    }

}]);



EventsApp.controller('EventCtrl', ['$scope', '$http', 'EventStorage', function($scope, $http, EventStorage)
{
    $scope.storage = EventStorage;

    $scope.getCurrentCount = function()
    {
        return $scope.storage.count();
    };

    $scope.getTotal = function()
    {
        return $scope.storage.getTotal();
    };

    $scope.init = function()
    {
        $scope.storage.fetch();
    }

    $scope.showMore = function()
    {
        $scope.storage.fetchMoreEvents();
    }

    $scope.init();
}

]);


EventsApp.directive('popOverCountries', function ($compile) {

    return {
        restrict: "A",
        transclude: true,
        template: "<span ng-transclude></span>",

        link: function (scope, element, attrs) {

            var event = scope.storage.find(parseInt(attrs.eventId));

            if(event == null){
                return ;
            }

            var scopeParam = scope.$new();

            scopeParam.countries = event.offer.country;

            var html = $('#popover-countries').html();

            popOverContent = $compile(html)(scopeParam);

            var options = {
                content: popOverContent,
                placement: "bottom",
                html: true,
                trigger: 'hover'
            };
            $(element).popover(options);
        }
    };
});

/*
* Ajax overlay 1.0
* Author: Simon Ilett @ aplusdesign.com.au
* Descrip: Creates and inserts an ajax loader for ajax calls / timed events 
* Date: 03/08/2011 
*/
function ajaxLoader (el, options) {
	// Becomes this.options
	var defaults = {
		bgColor 		: '#fff',
		duration		: 0,
		opacity			: 0.7,
		classOveride 	: false
	}
	this.options 	= jQuery.extend(defaults, options);
	this.container 	= $(el);
	
	this.init = function() {
		var container = this.container;
		// Delete any other loaders
		this.remove(); 
		// Create the overlay 
		var overlay = $('<div></div>').css({
				'background-color': this.options.bgColor,
				'opacity':this.options.opacity,
				'width':container.width(),
				'height':container.height(),
				'position':'absolute',
				'top':'0px',
				'left':'0px',
				'z-index':99999
		}).addClass('ajax_overlay');
		// add an overiding class name to set new loader style 
		if (this.options.classOveride) {
			overlay.addClass(this.options.classOveride);
		}
		// insert overlay and loader into DOM 
		container.append(
			overlay.append(
				$('<div></div>').addClass('ajax_loader')
			).show(0)
			//fadeIn(this.options.duration)
		);
    };
	
	this.remove = function(){
		var overlay = this.container.children(".ajax_overlay");
		if (overlay.length) {
			/*overlay.fadeOut(this.options.classOveride, function() {
				overlay.remove();
			});*/
			overlay.hide(0);
			overlay.remove();
		}	
	}

    this.init();
}	
	