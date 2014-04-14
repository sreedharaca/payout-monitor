
var OffersApp = angular.module('offersApp', ['ngResource']);


OffersApp.factory('FormData', function(){

    var FormData = {};

    return FormData;
});

OffersApp.factory('SortData', function(){

    var SortData = {
        sort_column: 'payout',
        sort_asc: false
    };

    return SortData;
});

OffersApp.factory('AnalogsResource', ['$resource',  function($resource){

    return $resource('/app_dev.php/offer/:id/analogs',
        {},
        {
            query:{method:'GET',  params:{id:null}},
            get:{method:'GET',  params:{id:null}},
            getAnalogs:{method:'POST',  params:{id:null}}
        }
    )
}]);

OffersApp.factory('AnalogOffersCache', [ '$http', 'AnalogsResource', 'FormData', function($http, AnalogsResource, FormData){

    var AnalogOffersCache = {};

    //{ id: [{offer},..,{offer}] }
    AnalogOffersCache.data = {};

    AnalogOffersCache.find = function(id)
    {
//        console.log('Start find analog offers');

        if(!id){
            throw 'Invalid id';
        }

        if(this.exists(id))
        {
            return this.data[id]
        }

        return null;
    }

    AnalogOffersCache.exists = function(id)
    {
        if( this.data[id] != undefined ){
            return true;
        }
        else{
            return false;
        }

    }

    AnalogOffersCache.setData = function(id, data)
    {
        this.data[id] = data;
    }


    AnalogOffersCache.fetch = function(id)
    {
        if(!this.exists(id))
        {
            var params = FormData;
            params.offer_id = id;//Storage.count();

    //        preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);

            $http({
                method  : 'POST',
                url     : '/app_dev.php/offer/analogs',
                data    : $.param(params),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
            .success(function(response) {
//console.log(response);
                if (!response.success) {
                    console.log('error');
                } else {
                    AnalogOffersCache.setData( id, response.analogs );
                }
    //                if(preloader){ preloader.remove(); }
            });

        }
    }


    return AnalogOffersCache;
}]);

OffersApp.factory('OffersData', [ '$http', 'FormData', 'SortData', function ($http, FormData, SortData) {

    var OffersData = {

        offers: [],

        names: [],

        total: 0,

        count: function(){
            return this.offers.length;
        },

        getTotal: function(){
            return this.total;
        },

        setTotal: function(val){
            return this.total = val;
        },

        append: function(offers){
            this.offers = this.offers.concat(offers);
        },

        find: function(id){

            if(!id){
                return null;
            }

            var retval = null;

            $.each(this.offers, function(index, offer){

                if(retval !== null){ return ; }

//                $.each(offersGroup.offers, function(index, offer){

                    if(offer.id == id){

                        retval = offer;
//                        return false;
                    }
//                });
            });

//            $.each(this.androidOffers, function(index, offersGroup){
//
//                if(retval !== null){ return ; }
//
//                $.each(offersGroup.offers, function(index, offer){
//
//                    if(offer.id == id){
//
//                        retval = offer;
//                        return false;
//                    }
//                });
//
//            });

            return retval;
        },

        fetch: function() {

            var params = FormData;
            $.extend(params, SortData);
            params.offset = 0;//Storage.count();

            preloader = new ajaxLoader(document.body /*, {classOveride: 'blue-loader'}*/);

            $http({
                method  : 'POST',
                url     : offer_filter_url,
                data    : $.param(params),  // pass in data as strings
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
            .success(function(response) {

                if (!response.success) {
                    console.log('error');
                } else {

                    OffersData.offers = response.offers;
                    OffersData.setTotal(response.totalCount);
                    OffersData.names = response.names;
                }

                if(preloader){ preloader.remove(); }
            });

        },

        fetchNextPage: function(){

            var params = FormData;
            $.extend(params, SortData);
            params.offset = this.count();

            preloader = new ajaxLoader(document.body);

            $http({
                url     : offer_filter_url,
                data    : $.param(params),
                method  : 'POST',
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }  // set the headers so angular passing info as form data (not request payload)
            })
            .success(function(response) {

                OffersData.append( response.offers );
                OffersData.setTotal( response.totalCount );
                OffersData.names = response.names;

                if(preloader){ preloader.remove(); }
            });

        }

    };

    return OffersData;
}]);


OffersApp.controller('FilterFormCtrl', ['$scope', '$http', 'OffersData', 'FormData', function ($scope, $http, OffersData, FormData) {

    $scope.formData = FormData;

    $scope.apps = OffersData.names;

    $scope.searchTextFocus = false;

    $scope.showSearchHint = false;

    $scope.init = function() {
        $scope.formData.platform = [3];
    }

    $scope.submit = function() {
        OffersData.fetch();
    }

    $scope.init();

}]);


OffersApp.controller('OffersCtrl', ['$scope', 'OffersData', 'SortData', 'AnalogOffersCache', function($scope, OffersData, SortData, AnalogOffersCache)
{
    $scope.OffersData = OffersData;

    $scope.AnalogOffersCache = AnalogOffersCache;

    $scope.sort_desc_img = '/images/sort_desc.png';

    $scope.sort_asc_img = '/images/sort_asc.png';

    $scope.SortData = SortData;

    $scope.sort_col = function(col_name)
    {
        if($scope.SortData.sort_column == col_name)
        {
            $scope.invert_sort();
        }
        else{
            $scope.SortData.sort_column = col_name;
        }

        $scope.OffersData.fetch();
    }

    $scope.invert_sort = function()
    {
        $scope.SortData.sort_asc = !$scope.SortData.sort_asc;
    }

    $scope.getCurrentCount = function()
    {
        return $scope.OffersData.count();
    };

    $scope.getTotal = function()
    {
        return $scope.OffersData.getTotal();
    };

    $scope.find = function(id){

        if(!id){
            return null;
        }

        return $scope.OffersData.find(id);
    }

    $scope.showMore = function()
    {
        $scope.OffersData.fetchNextPage();
    }

}]);


OffersApp.directive('popOver', ['$compile', 'AnalogOffersCache', function ($compile, AnalogOffersCache) {

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

            var offer_id = parseInt(attrs.offerId);

//            var offers = AnalogOffersCache.find(offer_id);
//
//            if(!offers){
//                return ;
//            }

            var scopeParam = scope.$new();
            scopeParam.AnalogOffersCache = AnalogOffersCache;
            scopeParam.offer_id = offer_id;

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
//                live: true
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
}]);


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


OffersApp.controller('TestCtrl', ['$scope', 'AnalogsResource', 'AnalogOffersCache', function($scope, Analogs, AnalogOffersCache){

    $scope.init = function()
    {
        var offers = Analogs
            .get(
                {id: 50},
                function(response){
                    console.assert(response.analogs != undefined, 'analogs array must be not undefined');
                    console.assert(Object.keys(response.analogs).length > 0, 'count of analogs must be > 0');
//                    console.log(response);
                }
            )
            ;
    }

    $scope.testAnalogsCache = function()
    {
        //set up
        AnalogOffersCache.data = { 5: [
            {id: 10, name: 'Spartan'}
        ]};

        console.assert(AnalogOffersCache.find(4) == null, 'it must return null value');
        console.assert(AnalogOffersCache.find(5)[0].id == 10, 'it must return array of one object with id attribute equals 10');

        //tear down
        AnalogOffersCache.data = {};
    }

    $scope.init();
    $scope.testAnalogsCache();

}]);
