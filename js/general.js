var THEMEMAKERS_EVENT_COUNTDOWN = function(start, container_id) {
	var self = {
		diff_time: null,
		container: null,
		intervalID: null,
		init: function() {
			var now_date = new Date();
			self.diff_time = start - now_date / 1000;
			self.container = jQuery(container_id);
			self.update_timer_view();
			self.intervalID = setInterval(self.update_timer_view, 999);
		},
		update_timer_view: function() {
			self.diff_time--;
			if (self.diff_time <= 0) {
				clearInterval(self.intervalID);
				return;
			}
			//*****
			jQuery(self.container).find('span.event-numbers').eq(0).html(self.get_days(self.diff_time));
			jQuery(self.container).find('span.event-numbers').eq(1).html(self.get_hours(self.diff_time));
			jQuery(self.container).find('span.event-numbers').eq(2).html(self.get_minutes(self.diff_time));
			jQuery(self.container).find('span.event-numbers').eq(3).html(self.get_seconds(self.diff_time));
		},
		get_days: function(seconds) {
			var days = parseInt(seconds / (60 * 60 * 24));
			days = (days < 10 ? "0" + days : days);
			return days;
		},
		get_hours: function(seconds) {
			var hours = parseInt((seconds / (60 * 60)) % 24);
			hours = (hours < 10 ? "0" + hours : hours);
			return hours;
		},
		get_minutes: function(seconds) {
			var minutes = parseInt((seconds / (60)) % 60);
			minutes = (minutes < 10 ? "0" + minutes : minutes);
			return minutes;
		},
		get_seconds: function(seconds) {
			var sec = parseInt(seconds % 60);
			sec = (sec < 10 ? "0" + sec : sec);
			return sec;
		}
	};

	return self;
};


var THEMEMAKERS_EVENT_CALENDAR = function(container_id, arguments, is_widget, timezone_string) {

	var self = {
		arguments: arguments,
		init: function() {
			var date = new Date();
			var d = date.getDate();
			var m = parseInt(date.getMonth());
			var y = date.getFullYear();
			var action = 'app_events_get_calendar_data';

			var day_names_short = [lang_sun, lang_mon, lang_tue, lang_wed, lang_thu, lang_fri, lang_sat];

			if (is_widget) {
				action = 'app_events_get_widget_calendar_data';

				var i;

				for(i in day_names_short){
					day_names_short[i] = day_names_short[i].substr(0,1);
				}
			}

			jQuery(".calendar_event_tooltip_close").live('click', function() {
				jQuery(this).parent().hide(222, function() {
					jQuery(this).remove();
				});

				return false;
			});

			var time_format = "H:mm";
			if (events_time_format=='1') {
				time_format = "h(:mm)tt";
			}

			jQuery(container_id).fullCalendar({
				theme: false,
				header: {
					left: self.arguments.header.left,
					center: self.arguments.header.center,
					right: self.arguments.header.right
				},
				editable: false,
				firstDay: self.arguments.first_day,
				monthNames: [lang_january, lang_february, lang_march, lang_april, lang_may, lang_june, lang_july, lang_august, lang_september, lang_october, lang_november, lang_december],
				monthNamesShort: [lang_jan, lang_feb, lang_mar, lang_apr, lang_may, lang_jun, lang_jul, lang_aug, lang_sep, lang_oct, lang_nov, lang_dec],
				dayNames: [lang_sunday, lang_monday, lang_tuesday, lang_wednesday, lang_thursday, lang_friday, lang_saturday],
				dayNamesShort: day_names_short,
				ignoreTimezone: 1,
				eventSources: [
					{
						url: ajaxurl,
						type: 'POST',
						data: {
							action: action
						},
						error: function() {
							//alert(error_fetching_events); // for developing
						},
						color: '', // a non-ajax option
						textColor: '' // a non-ajax option
					}
				],
				timeFormat: time_format,
				eventClick: function(calEvent, jsEvent, view) {
					//window.open(event.url, 'gcalevent', 'width=700,height=600');
					return true;
				},
				eventMouseover: function(calEvent, jsEvent, view) {

					jQuery(".calendar_event_tooltip").remove();

					function AddZero(num) {
						return (num >= 0 && num < 10) ? "0" + num : num + "";
					}
					var strDateTime = [];

					if(events_date_format == 1){
						strDateTime = [[AddZero(calEvent.start.getDate()), AddZero(calEvent.start.getMonth() + 1), calEvent.start.getFullYear()].join("/"), [AddZero(calEvent.start.getHours()), AddZero(calEvent.start.getMinutes())].join(":")].join(" ");
					}else{
						strDateTime = [[AddZero(calEvent.start.getMonth() + 1), AddZero(calEvent.start.getDate()), calEvent.start.getFullYear()].join("/"), [AddZero(calEvent.start.getHours()), AddZero(calEvent.start.getMinutes())].join(":")].join(" ");
					}

					var string1 = "";

					if (calEvent.featured_image_src !== undefined) {
						if (calEvent.featured_image_src.length > 0) {
							string1 = jQuery('<a>').addClass('calendar_event_tooltip_url')
									.attr('href', calEvent.url)
									.append(jQuery('<img>').addClass('calendar_event_tooltip_img')
									.attr('src', calEvent.featured_image_src)
									.attr('alt', calEvent.title));
						}
					}else{
						return false;
					}
                                        
                    if (events_time_format==1) { 
                        strDateTime=strDateTime.split(' ');
                        strDateTime=strDateTime[0];
                        var hours = calEvent.start.getHours();                                          
                        var minutes=calEvent.start.getMinutes();
                        var ap = "am";                                            
                        if (hours   > 11) { ap = "pm";        }
                        if (hours   > 12) { hours = hours - 12; }
                        if (hours   == 0) { hours = 12;        }
                        if (minutes == 0) { minutes=''};
                        strDateTime = strDateTime + ' ' + hours + (minutes!=''? ':' :'') + minutes + ap;
                    };
                                        
					var string2 = jQuery('<span>').addClass('calendar_event_tooltip_timezone')
							.html("<b>" + lang_time + "</b>: " + strDateTime + " " + timezone_string);

					var string3 = jQuery('<span>').addClass('calendar_event_tooltip_place')
							.html("<b>" + lang_place + "</b>: " + (calEvent.event_place_address != '' ? calEvent.event_place_address : ' -'));

					var string4 = jQuery('<span>').addClass('calendar_event_tooltip_description')
							.html(calEvent.post_excerpt);

					jQuery('<span>').addClass('calendar_event_tooltip')
                        .css('top', jsEvent.pageY)
                        .css('left', jsEvent.pageX)
                        .html("<h4><a href='" + calEvent.url + "'>" + calEvent.title + "</a></h4>")
                        .append(string1)
                        .append(string2)
                        .append(string3)
                        .append(string4)
                        .appendTo('body');

					return true;
				},
				eventMouseout: function(calEvent, jsEvent, view) {
					jQuery(".calendar_event_tooltip").remove();
					return true;
				}
			});

		}

	};

	return self;
};



var THEMEMAKERS_EVENT_EVENTS_LISTING = function() {
	var self = {
		floor_month: null,
		floor_year: null,
		displayed_month_num: null,
		displayed_year_num: null,
		next_time: null,
		prev_time: null,
		curent_events_time: null,
		events_on_page: 0,
		current_event_page: 0, //for pagination
		articles_on_page: 5,
		init: function(options) {

			self.floor_month = self.get_current_month();
			self.floor_year = self.get_current_year();
			self.displayed_month_num = self.floor_month;
			self.displayed_year_num = self.floor_year;
			self.next_time = self.prev_time = 0;
			self.articles_on_page = options['count'];

            self.update_events_listing(options);

			jQuery(".js_next_events_page").on('click', function() {
                jQuery(".js_prev_events_page").show();

				var opts = options;
				opts['start'] = self.next_time;
				opts['end'] = 0;

				self.update_events_listing(opts);
				return false;
			});

			jQuery(".js_prev_events_page").on('click', function() {
				var opts = options;
				opts['start'] = self.prev_time;
				opts['end'] = 0;

				self.update_events_listing(opts);
				return false;
			});

			jQuery('.events_listing_navigation a').live('click', function() {
                if(jQuery(this).hasClass('current')){
                    return false;
                }
				var page_id = jQuery(this).data('page-id');
				jQuery('.events_listing_navigation a').removeClass('current');
				jQuery('[data-page-id=' + page_id + ']').addClass('current');
				jQuery("#events_listing").find('article').hide(200);
				for (var i = page_id * self.articles_on_page; i < page_id * self.articles_on_page + self.articles_on_page; i++) {
					jQuery("#events_listing").find('article').eq(i).show(200);
				}
                self.current_event_page = page_id;
                self.check_pagination();
				jQuery('html, body').scrollTop(0);
				return false;
			});

			jQuery("#app_event_listing_categories").change(function() {
				var opts = options;
				opts['start'] = self.curent_events_time;
				opts['end'] = 0;
				opts['category'] = 0;

				self.update_events_listing(opts);
			});

		},
		update_events_listing: function(options) {
			jQuery('#infscr-loading').animate({opacity: 'show'}, 333);

			self.curent_events_time = options['start'];

            if (!options['category']) {
	            options['category'] = 0;
            }

			if (jQuery("#app_event_listing_categories").length) {
				options['category'] = jQuery("#app_event_listing_categories").val();
			}

			var data = {
				action: "app_events_get_events_listing",
				events_list_args: options
			};
			jQuery.post(ajaxurl, data, function(response) {
				response = jQuery.parseJSON(response);

				self.next_time = response['next_time'];
				self.prev_time = response['prev_time'];

				jQuery("#events_listing_month").html(response['month']);
				jQuery("#events_listing_year").html(response['year']);

				self.displayed_month_num = parseInt(response['month_num'], 10);
				self.displayed_year_num = parseInt(response['year'], 10);

				if (response['html'].length > 11) {
					jQuery("#events_listing").html(response['html']);
				} else {
					jQuery("#events_listing").html('<li class="tmm_no_events">' + tmm_lang_no_events + '</li>');
				}


				self.events_on_page = parseInt(response['count']);
				self.draw_pagination();
                self.check_pagination();
                
				if (self.displayed_year_num == self.floor_year) {
					if (self.displayed_month_num <= self.floor_month) {
						jQuery(".js_prev_events_page").hide();
					}
				}

				jQuery('#infscr-loading').animate({opacity: 'hide'}, 333);

			});
		},
        check_pagination: function() {
			if(self.events_on_page > self.articles_on_page){
                jQuery('.events_listing_navigation').show();

            }else{
                jQuery('.events_listing_navigation').hide();
            }
		},
		draw_pagination: function() {
			jQuery("#events_listing").find('article').hide();
			//***
			jQuery(".events_listing_navigation").html("");
			for (var i = 0; i < Math.ceil(self.events_on_page / self.articles_on_page); i++) {
				var css_class = 'page-numbers';
				var pagination_string = jQuery('<a>').addClass(css_class).attr('href', '#').attr('data-page-id', i).text(i + 1);
				jQuery('.events_listing_navigation').append(pagination_string);
			}

			jQuery('.events_listing_navigation a').eq(0).trigger('click');
		},
		daysInMonth: function(month, year) {
			return new Date(year, month, 0).getDate();
		},
		get_current_month: function() {
			var d = new Date();
			return parseInt(d.getMonth(), 10) + 1;
		},
		get_current_year: function() {
			var d = new Date();
			return parseInt(d.getFullYear(), 10);
		},
		get_mk_time: function() {
			var d = new Date();
			return Math.floor(d.getTime() / 1000);//sec
		}
	};

	return self;
};
