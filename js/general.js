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

					var strDateTime = [],
						tooltip_html = '<span class="calendar_event_tooltip" style="top:' + jsEvent.pageY + 'px;left:' + jsEvent.pageX + 'px;">';

					/* Tooltip title */
					tooltip_html += '<h4><a href="' + calEvent.url + '">' + calEvent.title + '</a></h4>';

					/* Tooltip image */
					if (events_show_tooltip_image) {
						if (calEvent.featured_image_src !== undefined && calEvent.featured_image_src.length > 0) {
							tooltip_html += '<a class="calendar_event_tooltip_url" href="' + calEvent.url + '">' +
												'<img class="calendar_event_tooltip_img" src="' + calEvent.featured_image_src + '" alt="' + calEvent.title + '" />' +
											'</a>';
						}
					}

					/* Tooltip time */
					if (events_show_tooltip_time) {

						if(events_date_format == 1){
							strDateTime = [[AddZero(calEvent.start.getDate()), AddZero(calEvent.start.getMonth() + 1), calEvent.start.getFullYear()].join("/"), [AddZero(calEvent.start.getHours()), AddZero(calEvent.start.getMinutes())].join(":")].join(" - ");
						}else{
							strDateTime = [[AddZero(calEvent.start.getMonth() + 1), AddZero(calEvent.start.getDate()), calEvent.start.getFullYear()].join("/"), [AddZero(calEvent.start.getHours()), AddZero(calEvent.start.getMinutes())].join(":")].join(" - ");
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

						tooltip_html += '<span class="calendar_event_tooltip_timezone">' +
											'<b>' + lang_time + '</b>: ' + strDateTime + ' ' + timezone_string +
										'</span>';
					}

					/* Tooltip place */
					if (events_show_tooltip_place) {
						tooltip_html += '<span class="calendar_event_tooltip_place">' +
											'<b>' + lang_place + '</b>: ' + (calEvent.event_place_address != '' ? calEvent.event_place_address : ' -') +
										'</span>';
					}

					/* Tooltip description */
					if (events_show_tooltip_desc) {
						var desc = calEvent.post_excerpt;

						if (events_tooltip_desc_symbols_count && desc.length > events_tooltip_desc_symbols_count) {
							desc = desc.substr(0, events_tooltip_desc_symbols_count) + ' ...';
						}

						tooltip_html += '<span class="calendar_event_tooltip_description">' + desc + '</span>';
					}

					/* Tooltip closing span */
					tooltip_html += '</span>';

					jQuery('body').append(tooltip_html);

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
		curent_events_time: null,
		events_on_page: 0,
		current_event_page: 0, //for pagination
		articles_on_page: 5,
		init: function(options) {

			self.floor_month = self.get_current_month();
			self.floor_year = self.get_current_year();
			self.articles_on_page = parseInt(options['count']);

			if( jQuery("#event_listing_period").length && !jQuery("#events_listing_month").length ) {

				if (jQuery('.single-title').length) {
					jQuery('.single-title').children().eq(0).append('&nbsp;<span id="events_listing_month"></span> <span id="events_listing_year"></span>');
				}
			}

            self.update_events_listing(options);

			jQuery("#event_listing_period").change(function() {
				var opts = options;
				opts['start'] = jQuery(this).val();
				opts['end'] = 0;

				self.update_events_listing(opts);
			});

			jQuery('.events_listing_navigation a').live('click', function() {

                if(jQuery(this).hasClass('current')){
                    return false;
                }

				jQuery('.events_listing_navigation a').removeClass('current');
				jQuery(this).addClass('current');
				jQuery('#events_listing > article').hide(200);

				var page_id = jQuery(this).data('page-id'),
					i = page_id * self.articles_on_page,
					ic = page_id * self.articles_on_page + self.articles_on_page;

				for (i; i < ic; i++) {
					jQuery("#events_listing").find('article').eq(i).show(200);
				}

                self.current_event_page = page_id;
                self.check_pagination();
				jQuery('html, body').scrollTop(0);
				return false;
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

				jQuery("#events_listing_month").html(response['month']);
				jQuery("#events_listing_year").html(response['year']);

				if (response['html'].length > 11) {
					jQuery("#events_listing").html(response['html']);
				} else {
					jQuery("#events_listing").html('<li class="tmm_no_events">' + tmm_lang_no_events + '</li>');
				}

				self.events_on_page = parseInt(response['count']);
				self.draw_pagination();
                self.check_pagination();

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
