
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

//        relativeOffers: [],

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
                }

                if(preloader){ preloader.remove(); }
            });
    }
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