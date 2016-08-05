/*!========================================================================
 * Flexify Datepicker 1.0.0
 * Author: Pham Dinh Long | longfanos@gmail.com | (@longfanos}
 * ========================================================================*/
 ;(function($) {
	$.fn.getHiddenHeight = function(){
		var $clone = this.clone().appendTo('body'),
			height = $clone.outerHeight();
		$clone.remove();
		return height;
	};
})(jQuery);

;(function($, window, document, undefined) {  

	var lang = {
			en: {
				month: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
				short_month: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
				short_day: ["S", "M", "T", "W", "T", "F", "S"]		
			},
			vi: {
				month: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
				short_month: ["T01", "T02", "T03", "T04", "T05", "T06", "T07", "T08", "T09", "T10", "T11", "T12"],
				short_day: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"]		
			}			
		};
		
	var formats = {
		'd/m/Y': { reg: new RegExp(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/), pos: {day: 1, month: 2, year: 3}, short_format: 'd/m' },
		'm/d/Y': { reg: new RegExp(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/), pos: {day: 2, month: 1, year: 3}, short_format: 'm/d' },
		'Y/m/d': { reg: new RegExp(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/), pos: {day: 3, month: 2, year: 1}, short_format: 'm/d' }
	};

    var defaults = {
			lang: 'en',
			month_names: [],
			short_month_names: [],
			short_day_names: [],
			error_out_of_range: "Selected date is out of range",
			selectable_days: [0, 1, 2, 3, 4, 5, 6],
			non_selectable: [],
			rec_non_selectable: [],
			start_of_week: 1,
			show_week: 0,
			select_week: 0,
			week_label: "",
			date_min: "",
			date_max: "",
			date_format: 'm/d/Y',
			show_clear: true
        };

    var Datepicker = function(element, options) {
        this.$element = $(element);
		this.options = $.extend(defaults, options || {});
		this.month_names = lang[this.options.lang].month;
		this.short_month_names = lang[this.options.lang].short_month; 		
		this.short_day_names = lang[this.options.lang].short_day; 
		
		this.init();
    }
		
	Datepicker.prototype = {

		init: function() {
			var self = this;
			this.$element.attr('autocomplete', 'off');
			
			if(this.options.show_clear == true){
				var inputHeight = this.$element[this.$element.is(':hidden') ? 'getHiddenHeight' : 'outerHeight']();
				var $button = $('<span class="datepicker-clearer" style="height:'+(inputHeight)+'px;line-height:'+(inputHeight-1)+'px">x</span>');
				$button.on('click.flexify.datepicker', function(){
					self.$element.val(" "); 
				});
				this.$element.css('float', 'left').after($button);
			}	

			if (this.options.date_max != "") {
				this.options.date_max = this.stringToDate(this.options.date_max); 
			}
			if (!this.options.date_max) {
				this.options.date_max = "";
			}
			
			if (this.options.date_min != "") {
				this.options.date_min = this.stringToDate(this.options.date_min); 
			}
			if (!this.options.date_min) {
				this.options.date_min = "";
			}
			
			this.$wrapper = $('#datepicker');
			this.$nav = $('#datepicker_nav');
			
			this.$tbody = this.$wrapper.find('tbody');
			
			this.$error = this.$nav.find('.error');
			this.$navMonth = this.$nav.find('.nav-month');
			this.$navYear = this.$nav.find('.nav-year');	

			this.$element.on('focus.flexify.datepicker', function(){ self.show(); });
		},
	  		
		selectMonth: function(date) {
			var self = this,
				options = this.options,
				newMonth = new Date(date.getFullYear(), date.getMonth(), date.getDate());
			
			if (this.isNewDateAllowed(newMonth)) {

				this.$navMonth.find('.name').html(this.month_names[date.getMonth()]);
				this.$navYear.find('.name').html(date.getFullYear());				
				
				//if (!this.currentMonth || !(this.currentMonth.getFullYear() == newMonth.getFullYear() && this.currentMonth.getMonth() == newMonth.getMonth())) {

					this.currentMonth = newMonth;
					//this.$navMonth.find('.name').html(this.month_names[date.getMonth()]);
					//this.$navYear.find('.name').html(this.currentMonth.getFullYear());
					
					var range 			= this.range(date),
						numdays 		= this.between(range.start, range.end),
						currentDay 		= null,
						firstDayOfWeek 	= null,
						lastDayOfWeek 	= null,
						selectClass		= '',
						cells 			= "";
						
					for (var i = 0; i <= numdays; i++) {
						currentDay = new Date(range.start.getFullYear(), range.start.getMonth(), range.start.getDate() + i, 12, 00);

						if (this.isFirstDayOfWeek(currentDay)) {
							firstDayOfWeek = currentDay;
							lastDayOfWeek = new Date(currentDay.getFullYear(), currentDay.getMonth(), currentDay.getDate()+6, 12, 00);

							if(options.select_week && this.isNewDateAllowed(firstDayOfWeek)) {
								cells += "<tr date='" + this.dateToString(currentDay) + "' class='selectable_week'>";
							} else {
								cells += "<tr>";
							}						
							if (options.show_week == 1) {
								cells += '<td class="week_num">'+this.getWeekNum(currentDay)+'</td>';
							}
						}

						if ((options.select_week == 0 && currentDay.getMonth() == date.getMonth() && this.isNewDateAllowed(currentDay) && !this.isHoliday(currentDay)) || (options.select_week==1 && currentDay.getMonth() == date.getMonth() && this.isNewDateAllowed(firstDayOfWeek))) {
							selectClass = 'selectable_day';
						} else {
							selectClass = 'unselected_month';
						}
						
						cells += '<td class="'+selectClass+'" date="' + this.dateToString(currentDay) + '">' + currentDay.getDate() + '</td>';
						
						if (this.isLastDayOfWeek(currentDay)) {
							cells += "</tr>";
						}
					};

					this.$tbody.html(cells).find('td[date="' + this.dateToString(new Date()) + '"]').addClass("today");
				//};
				
				this.$tbody.find('.selected').removeClass('selected');
				this.$tbody.find('td[date="' + this.selectedDateString + '"], tr[date="' + this.selectedDateString + '"]').addClass('selected');
				
			} else {
				this.show_error(options.error_out_of_range);
			}
		},
	  
		selectDate: function(date) {
			
			if (typeof(date) == "undefined") {	
				date = this.stringToDate(this.$element.val());
			};

			if (!date) {
				date = new Date();
			}

			if (this.options.select_week == 1 && !this.isFirstDayOfWeek(date)){
				date = new Date(date.getFullYear(), date.getMonth(), (date.getDate() - date.getDay() + this.options.start_of_week), 12, 00);	
			}

			if (this.isNewDateAllowed(date)) {
				this.selectedDate = date;
				this.selectedDateString = this.dateToString(this.selectedDate);
				this.selectMonth(this.selectedDate);
			} else if((this.options.date_min) && this.between(this.options.date_min, date)<0) {
				this.selectedDate = this.options.date_min;
				this.selectMonth(this.options.date_min);
				this.$element.val(" ");
			} else {
				this.selectMonth(this.options.date_max);
				this.$element.val(" ");
			}
		},
	  
		isNewDateAllowed: function(date){
			return ((!this.options.date_min) || this.between(this.options.date_min, date)>=0) && ((!this.options.date_max) || this.between(date, this.options.date_max)>=0);
		},

		isHoliday: function(date){
			return ((this.idx(this.options.selectable_days, date.getDay())===false || this.idx(this.options.non_selectable, this.dateToString(date))!==false) || this.idx(this.options.rec_non_selectable, this.dateToString(date, true))!==false);
		},
	  
		setDate: function(value) {
			this.$element.val(value);//.trigger('change.flexify.datepicker');
			this.hide();
		},
	  
		show: function() {
			var self = this;
			
			this.$element.attr('readonly', true);
			this.$error.hide();
			
			if (this.$wrapper.hasClass('active')) {
				this.destroy();
			}
			
			this.$wrapper.addClass('active').show();
			this.setPosition();
			this.selectDate();			
			
			this.$navMonth.on('click.flexify.datepicker', '.prev', function(){ self.moveMonthBy(-1) })
						  .on('click.flexify.datepicker', '.next', function(){ self.moveMonthBy(1) })
						  .on('dblclick.flexify.datepicker', '.name', function(){
								$(this).html(self.dropdownMonth()).on('change.flexify.datepicker', 'select', function(){
									self.moveMonthBy(parseInt($(this).find('option').filter(':selected').val()) - self.currentMonth.getMonth());
								});
						  });
					 
			this.$navYear.on('click.flexify.datepicker', '.prev', function(){ self.moveMonthBy(-12); })
						 .on('click.flexify.datepicker', '.next', function(){ self.moveMonthBy(12); })
						 .on('dblclick.flexify.datepicker', '.name', function(){
								if ($('.input').length === 0) {
									var currentYearValue = parseInt($(this).text()),
										$input = $('<input type="text" class="text input" value="'+ currentYearValue +'" />');
									
									$(this).html($input);
									$input.on('keyup.flexify.datepicker', function(){
										var val = this.value;
										if (val.length == 4 && val != currentYearValue) {
											self.moveMonthBy((parseInt(val) - currentYearValue)*12);
										} else if (val.length > 4) {
											$(this).val(val.substr(0, 4));
										}
									}).focus();
								}
						 });
			
			if (this.options.select_week == 0) {
				this.$tbody.on('click.flexify.datepicker', '.selectable_day', function(e){
					self.setDate($(e.target).attr('date'));
				});
			} else {
				this.$tbody.on('click.flexify.datepicker', '.selectable_week', function(e){
					self.setDate($(e.target.parentNode).attr('date'));
				});
			}		
			
			$(document).on('keydown.flexify.datepicker', function(e) { self.keydown(e); })
					   .on('click.flexify.datepicker', function(e) { self.close(e); });
		},
	  
		hide: function() {
			this.$element.removeAttr('readonly');
			this.$wrapper.removeClass('active').hide();
			this.destroy();
		},
		
		destroy: function() {
			this.$navMonth.off('.flexify.datepicker');
			this.$navYear.off('.flexify.datepicker');			
			this.$tbody.off('.flexify.datepicker');
			$(document).off('.flexify.datepicker');				
		},
		
		close: function(e) {
			if (($(e.target).closest('#datepicker').length === 0) && (e.target != this.$element[0])) {
				this.hide();
			}
		},
		
		keydown: function(e) {
			switch (e.keyCode)
			{
			  case 9: 
			  case 27:
				this.hide();
				return;
			  break;
			  case 13:
				if (this.isNewDateAllowed(this.stringToDate(this.selectedDateString)) && !this.isHoliday(this.stringToDate(this.selectedDateString))) {
					this.setDate(this.selectedDateString);
				}	
			  break;
			  case 33:
				this.moveDateMonthBy(event.ctrlKey ? -12 : -1);
			  break;
			  case 34:
				this.moveDateMonthBy(event.ctrlKey ? 12 : 1);
			  break;
			  case 38:
				this.moveDateBy(-7);
			  break;
			  case 40:
				this.moveDateBy(7);
			  break;
			  case 37:
				if(this.options.select_week == 0) this.moveDateBy(-1);
			  break;
			  case 39:
				if(this.options.select_week == 0) this.moveDateBy(1);
			  break;
			  default:
				return;
			}
			e.preventDefault();
		},
	  
		stringToDate: function(string) {
			var format = formats[this.options.date_format];
			var matches = string.match(format.reg);
			if (!matches || (matches[3]==0 && matches[2]==0 && matches[1]==0)){
				return null;
			}
			return new Date(matches[format.pos.year], parseInt(matches[format.pos.month]-1), matches[format.pos.day]);
		},
		
	  	fixFormat: function(str) {
			return str.replace(/[d]+/g,'d').replace(/[m]+/g,'m').replace(/[Y]+/g,'Y');
		},
		
		dateToString: function(date, shorten) {
			
			var dateformat = this.options.date_format,
				formated;
				
			if (typeof(shorten) == 'undefined' || shorten === false) {
				formated = this.fixFormat(dateformat);
				return formated.replace('d', this.strpad(date.getDate())).replace('m', this.strpad(date.getMonth()+1)).replace('Y', date.getFullYear());
			} else {
				formated = this.fixFormat(formats[dateformat].short_format);
				return formated.replace('d', this.strpad(date.getDate())).replace('m', this.strpad(date.getMonth()+1));				
			}
		},
	  
		setPosition: function() {
			var offset = this.$element.offset();
			this.$wrapper.css({
				top: offset.top + this.$element.outerHeight(),
				left: offset.left
			});			
		},
	  
		moveDateBy: function(amount) {
			var newDate = new Date(this.selectedDate.getFullYear(), this.selectedDate.getMonth(), this.selectedDate.getDate() + amount);
			this.selectDate(newDate);
		},
	  
		moveDateMonthBy: function(amount) {
			var newDate = new Date(this.selectedDate.getFullYear(), this.selectedDate.getMonth() + amount, this.selectedDate.getDate());
			if (newDate.getMonth() == this.selectedDate.getMonth() + amount+1) {
				newDate.setDate(0);
			};
			this.selectDate(newDate);
		},
	  
		moveMonthBy: function(amount) {
			if (amount < 0) {
				var newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + amount+1, -1);
			} else {
				var newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + amount, 1);
			}
			this.selectMonth(newMonth);
		},
	  
		dropdownMonth:function(){
			var names = this.month_names, opts = '';
		
			for(var i = 0; i < names.length; i++){
				if(i==this.currentMonth.getMonth()) {
					opts += '<option value="'+(i)+'" selected="selected">'+names[i]+'</option>';
				} else {
					opts += '<option value="'+(i)+'">'+names[i]+'</option>';
				}
			}
			return '<select>' + opts + '</select>';
		},
	  
		idx: function(ary, v) {
			for (var i = 0; i < ary.length; i++) {
			  if (v == ary[i]) return i;
			};
			return false;
		},
	  
		between: function(start, end) {
			start = Date.UTC(start.getFullYear(), start.getMonth(), start.getDate());
			end = Date.UTC(end.getFullYear(), end.getMonth(), end.getDate());
			return (end - start) / 86400000;
		},
	  
		changeDayTo: function(dayOfWeek, date, direction) {
			var difference = direction * (Math.abs(date.getDay() - dayOfWeek - (direction * 7)) % 7);
			return new Date(date.getFullYear(), date.getMonth(), date.getDate() + difference);
		},

		range: function(date) {
			var start_of_week = this.options.start_of_week,
				year = date.getFullYear(),
				month = date.getMonth();
				
			return {
				start: this.changeDayTo(start_of_week, new Date(year, month), -1),
				end: this.changeDayTo((start_of_week - 1) % 7, new Date(year, month+1, 0), 1)
			};
		},
	  
		getWeekNum:function(date){
			var date_week = new Date(date.getFullYear(), date.getMonth(), date.getDate()+6),
				firstDayOfYear = new Date(date_week.getFullYear(), 0, 1, 12, 00),
				n = parseInt(this.between(firstDayOfYear, date_week)) + 1;
			return Math.floor((date_week.getDay() + n + 5)/7) - Math.floor(date_week.getDay() / 5);
		},
		
		isFirstDayOfWeek: function(date) {
			return date.getDay() == this.options.start_of_week;
		},
	 	  
		isLastDayOfWeek: function(date) {
			return date.getDay() == (this.options.start_of_week - 1) % 7;
		},
	  
		show_error: function(msg){
			this.$error.html(msg).show(0, function(){
				setTimeout("$(this).hide();", 2000);
			});
		},
	  
		strpad: function(num){
			var num = parseInt(num);
			return (num < 10) ? '0' + num : num;
		}
	};

	Datepicker.setup = function(options) {
		options = $.extend(defaults, options || {});

		var $wrapper = $('<div class="datepicker" id="datepicker" />');//.attr('unselectable', 'on').css('user-select', 'none').on('selectstart', false);

		var nav = '\
			<div class="nav" id="datepicker_nav">\
				<div class="error"></div>\
				<div class="nav-month"><span class="button prev">&#171;</span><span class="name"></span><span class="button next">&#187;</span></div>\
				<div class="nav-year"><span class="button prev">&#171;</span><span class="name"></span><span class="button next">&#187;</span></div>\
			</div>';
			
		var table = '<table><thead><tr>';			
		if(options.show_week == 1) {
			table +='<th class="week_label">'+(options.week_label)+'</th>';
		}		
		var short_day_names = lang[options.lang].short_day;
		for (var i = 0; i < short_day_names.length; i++) {
			table += '<th>' + short_day_names[(i + options.start_of_week) % 7] + '</th>';
		};
		table += '</tr></thead><tbody></tbody></table>';
	
		$wrapper.html(nav+table).appendTo('body');
	};
	
    $.fn.datepicker = function(options) {

		Datepicker.setup(options);
		
		return this.each(function() {
			if (!$.data(this, 'flexify.datepicker')) {
				$.data(this, 'flexify.datepicker', new Datepicker(this, options));
			}
		});	
	}
})(jQuery, window, document); 