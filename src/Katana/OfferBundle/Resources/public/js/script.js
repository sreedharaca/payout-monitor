
var OffersApp = angular.module('offersApp', []);

OffersApp.directive('popOver', function ($compile, $http) {

//    var itemsTemplate = "<ul class='unstyled'><li ng-repeat='item in items'>{{item}}</li></ul>";

    var getTemplate = function () {
//        return itemsTemplate;
//        $http.get('/angular/partials/competitors.html').then(function(response){
//            if(response.status == 200){
//                return response.data;
//            }
//        });

        return false;
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
                console.log('relative offers', offer.relative_offers);
            }
            else{
                console.log('nothing found');
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




OffersApp.controller('OffersCtrl', ['$scope', '$http', function($scope, $http)
{
//function OffersCtrl($scope, $http) {

    $scope.formData = {letters:{}};

    $scope.do = function() {

        console.log($scope.formData);

        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);
        //посылаем запрос на сервер - передаем данные формы - получаем ответ с json данными
        $http({
            method  : 'POST',
            url     : offer_filter_url,
            data    : $.param($scope.formData),  // pass in data as strings
            headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
        })
        .success(function(data) {
            console.log(data);

            if (!data.success) {

                console.log('error');
                // if not successful, bind errors to error variables
//                    $scope.errorName = data.errors.name;
//                    $scope.errorSuperhero = data.errors.superheroAlias;
            } else {
                // if successful, bind success message to message
                $scope.offers = data.iosOffers;
                $scope.androidOffers = data.androidOffers;

                //bind row to
            }

            if(preloader){ preloader.remove(); }
        });
    }

    $scope.offers = [];

    $scope.androidOffers = [];


    $scope.find = function(id){

//console.log('try find by id: ', id)

        if(!id){
            return null;
        }

        var retval = null;

        $.each($scope.offers, function(index, offersGroup){

            if(retval !== null){ return ; }

            $.each(offersGroup.offers, function(index, offer){

//console.log('looping offers', offer);
//console.log('condition equals:',offer.id,id)
                if(offer.id == id){

                    retval = offer;
                    return false;
                }
            });
        });

        $.each($scope.androidOffers, function(index, offersGroup){

            if(retval !== null){ return ; }

            $.each(offersGroup.offers, function(index, offer){

//console.log('looping offers', offer);

//console.log('condition equals:',offer.id,id)
                if(offer.id == id){

                    retval = offer;
                    return false;
                }
            });

        });

        return retval;
    }

}

]);