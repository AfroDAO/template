/**
 * @link https://github.com/rendro/countdown
 */
(function( $ ){
	!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var n;"undefined"!=typeof window?n=window:"undefined"!=typeof global?n=global:"undefined"!=typeof self&&(n=self),n.Countdown=e()}}(function(){var define,module,exports;return function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s}({1:[function(require,module,exports){var defaultOptions={date:"June 7, 2087 15:03:25",refresh:1e3,offset:0,onEnd:function(){return},render:function(date){this.el.innerHTML=date.years+" years, "+date.days+" days, "+this.leadingZeros(date.hours)+" hours, "+this.leadingZeros(date.min)+" min and "+this.leadingZeros(date.sec)+" sec"}};var Countdown=function(el,options){this.el=el;this.options={};this.interval=false;for(var i in defaultOptions){if(defaultOptions.hasOwnProperty(i)){this.options[i]=typeof options[i]!=="undefined"?options[i]:defaultOptions[i];if(i==="date"&&typeof this.options.date!=="object"){this.options.date=new Date(this.options.date)}if(typeof this.options[i]==="function"){this.options[i]=this.options[i].bind(this)}}}this.getDiffDate=function(){var diff=(this.options.date.getTime()-Date.now()+this.options.offset)/1e3;var dateData={years:0,days:0,hours:0,min:0,sec:0,millisec:0};if(diff<=0){if(this.interval){this.stop();this.options.onEnd()}return dateData}if(diff>=365.25*86400){dateData.years=Math.floor(diff/(365.25*86400));diff-=dateData.years*365.25*86400}if(diff>=86400){dateData.days=Math.floor(diff/86400);diff-=dateData.days*86400}if(diff>=3600){dateData.hours=Math.floor(diff/3600);diff-=dateData.hours*3600}if(diff>=60){dateData.min=Math.floor(diff/60);diff-=dateData.min*60}dateData.sec=Math.round(diff);dateData.millisec=diff%1*1e3;return dateData}.bind(this);this.leadingZeros=function(num,length){length=length||2;num=String(num);if(num.length>length){return num}return(Array(length+1).join("0")+num).substr(-length)};this.update=function(newDate){if(typeof newDate!=="object"){newDate=new Date(newDate)}this.options.date=newDate;this.render();return this}.bind(this);this.stop=function(){if(this.interval){clearInterval(this.interval);this.interval=false}return this}.bind(this);this.render=function(){this.options.render(this.getDiffDate());return this}.bind(this);this.start=function(){if(this.interval){return}this.render();if(this.options.refresh){this.interval=setInterval(this.render,this.options.refresh)}return this}.bind(this);this.updateOffset=function(offset){this.options.offset=offset;return this}.bind(this);this.start()};module.exports=Countdown},{}],2:[function(require,module,exports){var Countdown=require("./countdown.js");var NAME="countdown";var DATA_ATTR="date";jQuery.fn.countdown=function(options){return $.each(this,function(i,el){var $el=$(el);if(!$el.data(NAME)){if($el.data(DATA_ATTR)){options.date=$el.data(DATA_ATTR)}$el.data(NAME,new Countdown(el,options))}})};module.exports=Countdown},{"./countdown.js":1}]},{},[2])(2)});
})( jQuery );

(function( $, d ){

	function do_stopwatch() {
            function callback(){
		$( '.builder-countdown-holder' ).each(function(){
			var thiz = $( this );
			if( thiz.data( 'target-date' ) == '' ) return;

			thiz.countdown( {
				date : new Date( thiz.data( 'target-date' ) * 1000 ),
				render : function( data ){
					thiz.find( '.years .date-counter' ).text( this.leadingZeros( data.years, thiz.find( '.years' ).data( 'leading-zeros' ) ) );
					thiz.find( '.days .date-counter' ).text( this.leadingZeros( data.days, thiz.find( '.days' ).data( 'leading-zeros' ) ) );
					thiz.find( '.hours .date-counter' ).text( this.leadingZeros( data.hours, thiz.find( '.hours' ).data( 'leading-zeros' ) ) );
					thiz.find( '.minutes .date-counter' ).text( this.leadingZeros( data.min, thiz.find( '.minutes' ).data( 'leading-zeros' ) ) );
					thiz.find( '.seconds .date-counter' ).text( this.leadingZeros( data.sec, thiz.find( '.seconds' ).data( 'leading-zeros' ) ) );
				},
				onEnd : function(){
					window.location.reload();
				}
			} );
		});
            }
            if($( '.builder-countdown-holder' ).length>0){
                Themify.LoadAsync(builderCountDown.url+'core.min.js',function(){
                    
                    Themify.LoadAsync(builderCountDown.url+'datepicker.min.js',function(){
                        
                        Themify.LoadAsync(builderCountDown.url+'widget.min.js',function(){
                        
                            Themify.LoadAsync(builderCountDown.url+'mouse.min.js',function(){
                                
                                Themify.LoadAsync(builderCountDown.url+'slider.min.js',callback,builderCountDown.ver,false,function(){
                                    return typeof $.ui.slider!=='undefined';
                                });
                                
                            },builderCountDown.ver,false,function(){
                                return typeof $.ui.mouse!=='undefined';
                            });
                            
                        },builderCountDown.ver,false,function(){
                            return typeof $.widget!=='undefined';
                        });
                        
                    },builderCountDown.ver,false,function(){
                        return typeof $.ui.datepicker!=='undefined';
                    });
                    
                },builderCountDown.ver,false,function(){
                    return typeof $.ui!=='undefined';
                });
            }
	}
        $(document).ready(do_stopwatch);
	$( d ).on( 'ready', do_stopwatch );
	$( 'body' ).on( 'builder_load_module_partial builder_toggle_frontend', do_stopwatch );

})( jQuery, document );