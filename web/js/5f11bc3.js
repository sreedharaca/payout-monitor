
var OffersApp = angular.module('offersApp', []);

// Abstract resource
//OffersApp.factory('OffersResource', function($resource){
//
//    var baseURL = offer_filter_url,
//        baseParams = {
//            callback: 'JSON_CALLBACK'
//        },
//        baseOptions = {
//            query: {
//                method: 'JSONP'
//            }
//        }
//
//    return function(endpoint, params, options){
//        var url = baseURL + endpoint;
//
//        return $resource(url, params, options);
//    };
//
//});


//OffersApp.factory('OffersResource', function($resource){
//
//});


//создает объект модели
//который содерит в себе данные офферов
//умеет делегировать объекту ресурса получение офферов с сервера

OffersApp.factory('OffersData', function(){

    var OffersData = {

        offers: [],

        androidOffers: [],

        names: [], //{name: 'HUI'}, {name: 'pizda'}

        find: function(id){

            if(!id){
                return null;
            }

            var retval = null;

            $.each(this.offers, function(index, offersGroup){

                if(retval !== null){ return ; }

                $.each(offersGroup.offers, function(index, offer){

//console.log('looping offers', offer);
//console.log('condition equals:',offer.id,id);

                    if(offer.id == id){

                        retval = offer;
                        return false;
                    }
                });
            });

            $.each(this.androidOffers, function(index, offersGroup){

                if(retval !== null){ return ; }

                $.each(offersGroup.offers, function(index, offer){

//console.log('looping offers', offer);
//console.log('condition equals:',offer.id,id);

                    if(offer.id == id){

                        retval = offer;
                        return false;
                    }
                });

            });

            return retval;
        }
    };

    return OffersData;
});


OffersApp.controller('FilterFormCtrl', ['$scope', '$http', 'OffersData', function ($scope, $http, OffersData) {

    $scope.formData = {};

    $scope.searchTextFocus = false;

    $scope.showSearchHint = false;

//    $scope.OffersData = OffersData;

    $scope.apps = OffersData.names;

    $scope.do = function() {

//        console.log($scope.formData);

        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);
        //посылаем запрос на сервер - передаем данные формы - получаем ответ с json данными
        $http({
            method  : 'POST',
            url     : offer_filter_url,
            data    : $.param($scope.formData),  // pass in data as strings
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
            .success(function(data) {
//                console.log(data);

                if (!data.success) {

                    console.log('error');
                    // if not successful, bind errors to error variables
//                    $scope.errorName = data.errors.name;
//                    $scope.errorSuperhero = data.errors.superheroAlias;
                } else {

                    // if successful, bind success message to message
                    OffersData.offers = data.iosOffers;
                    OffersData.androidOffers = data.androidOffers;
                    $scope.apps = data.names;
                }

                if(preloader){ preloader.remove(); }
            });
    }

//    $scope.clear = function(){
//
//        $scope.formData = {};
//
//    };

}]);



OffersApp.controller('OffersCtrl', ['$scope', 'OffersData', function($scope, OffersData)
{
    $scope.OffersData = OffersData;

    $scope.find = function(id){

        if(!id){
            return null;
        }

        return $scope.OffersData.find(id);
    }

//    $scope.scrollTo = function(letter){
//
//        $location.hash(letter);
//
//        $anchorScroll();
//    }
}

]);


OffersApp.directive('popOver', function ($compile) {

    var getTemplate = function () {
    }

    return {
        restrict: "A",
        transclude: true,
        template: "<span ng-transclude></span>",
//        templateUrl: "/angular/partials/competitors.html",
        link: function (scope, element, attrs) {

//            console.log('scope', scope.dataOfferId);
//            console.log('attrs', attrs.offerId);

            var offer = scope.find(parseInt(attrs.offerId));

            if(offer !== null){
//                console.log('relative offers', offer.relative_offers);
            }
            else{
//                console.log('nothing found');
                return ;
            }

            var scopeParam = scope.$new();
            scopeParam.relative_offers = offer.relative_offers;

//            var popOverContent;

//            if (scope.competitors) {
            var html = $('#popover-content').html();
//
            popOverContent = $compile(html)(scopeParam);
//            }
            var options = {
                content: popOverContent,
                placement: "bottom",
                html: true
                /*live: true,*/
                /*trigger: 'click',*/
                /*title: 'HUI'*/
            };
            $(element).popover(options);
        }
//        scope: {
        /*popoverOffers: '=',*/
//            title: '@'
//            offerId: '@dataOfferId'
//        }
    };
});



OffersApp.directive('popOverCountries', function ($compile) {

    var getTemplate = function () {
    }

    return {
        restrict: "A",
        transclude: true,
        template: "<span ng-transclude></span>",

        link: function (scope, element, attrs) {

            var offer = scope.find(parseInt(attrs.offerId));

            if(offer == null){
                return ;
            }

            var scopeParam = scope.$new();
            scopeParam.countries = offer.countries;

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
	