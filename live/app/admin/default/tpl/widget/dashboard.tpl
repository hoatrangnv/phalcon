{{ if app_config('google_analytics_active') }}

<div id="charts_container">
    <div id="charts_content" style="width:calc(~'100% - 80px'); height: 300px;"></div>
</div>

<ul class="report-counter box-horizontal space-top">
    <li style="width:calc((100%/4) - 9px);">
        Số lượt truy cập
        <br>
        <div class="counter" id="ga_visits">0</div>
    </li>
    <li style="width:calc((100%/4) - 9px);">
        Số lượng khách truy cập
        <br>
        <div class="counter" id="ga_visitors">0</div>
    </li>    
    <li style="width:calc((100%/4) - 9px);">
        Số lần xem trang
        <br>
        <div class="counter" id="ga_pageviews">0</div>
    </li>    
    <li style="width:calc((100%/4) - 4px);">
        Số trang / Lượt truy cập
        <br>
        <div class="counter" id="ga_pageviewspervisit">0</div>
    </li>        
</ul>

<div class="box-horizontal space-top">
    <div class="left" style="width:calc((100%/2) - 8px);margin-right:10px;">
        <table class="table">
            <tr>
                <td colspan="2" class="head">Top trang giới thiệu</td>
            </tr>
            <tbody id="top_referrals"></tbody>    
        </table> 
    </div>  
    <div class="left" style="width:calc((100%/2) - 5px);">
        <table class="table">
            <tr>
                <td colspan="2" class="head">Top từ khóa tìm kiếm</td>
            </tr>
            <tbody id="top_keywords"></tbody>    
        </table>                  
    </div> 
</div>   

<script src="//code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
(function(){
    var tickInt = 7;    
      
    Highcharts.setOptions({
        colors:["#0877BE", "#009901", "#FF5717", "#B70000", "#722598", '#ED561B', '#DDDF00', '#24CBE5', "#A0522D"],
        lang: {
            months: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
            shortMonths: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            weekdays: ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'],
            loading: '<div style="font:25px Arial, sans-serif; color: #ccc;line-height:180px;">Đang tải dữ liệu...</div>'
        },
        credits:{
            enabled: false 
        }
    });      

    var chart = new Highcharts.Chart({
        chart:{
            renderTo: 'charts_content',            
            borderRadius:2,
            backgroundColor: '#ffffff',
            borderColor: '#cbcbcb',
            borderWidth: 0.5,
            margin:[40,10,30,10]          
        },   
        legend: { 
            align: 'left', verticalAlign: 'top', x:-10, y:0, borderWidth: 0, 
            itemStyle:{
                color:'#627485', font: '12px Arial,sans-serif'
            }, 
            symbolWidth:11, 
            symbolPadding:3 
        },                  
        plotOptions: {
            series:{
                animation: false, 
                fillOpacity: 0.05, 
                lineWidth: 4, 
                marker:{
                    symbol:'circle', 
                    lineWidth: 1
                }
            }         
        },                 
        series: [],                
        title:{
            text: null, 
            align:'left', 
            style:{
                font: 'bold 18px Arial,sans-serif', 
                color:'#777777'
            }
        },
        xAxis: {                 
            type: 'datetime',    
            categories: [],                                           
           // min: minTimes, max: maxTimes, tickInterval: tickInt * 24 * 3600 * 1000, // khoang cach thoi gian giua cac cot x               
            tickInterval: tickInt,
            tickWidth: 0, gridLineWidth: 0, gridLineColor: '#E7E7E7',                 
            labels: { 
                align: 'left', x:1, y: 15, 
                style:{
                    color: '#666666', font: '11px Arial,sans-serif'
                },            
                formatter: function(){
                    return Highcharts.dateFormat('%d/%m', this.value);
                }
            }
        },
        yAxis: [{ //Left
            title: '', gridLineWidth: 1, gridLineColor: '#E7E7E7', showFirstLabel: false,            
            labels: { 
                align: 'left', 
                x: 3, y: 12, 
                style:{
                    color: '#999999', font: '11px Arial,sans-serif'
                }, 
                formatter: function(){
                    return Highcharts.numberFormat(this.value, 0);
                }
            }                               
        }, 
        { //Right
            linkedTo: 0, opposite: true, 
            title: '', gridLineWidth: 1, gridLineColor: '#E7E7E7', showFirstLabel: false, 
            labels: {
                align: 'right', x: -3, y: 12, 
                style:{
                    color: '#999999', font: '11px Arial,sans-serif'
                },                
                formatter: function(){
                    return Highcharts.numberFormat(this.value, 0);
                }
            }
        }],                 
        tooltip: {
            xDateFormat: '<b>%A, ngày %d %B năm %Y</b>',
            shared: true, shadow:false, borderWidth: 2, borderRadius: 2, borderColor:'#8e9cab',
            style:{
                color:'#555',fontFamily: 'Arial,sans-serif', fontSize:'11px', padding: '4px'
            }
        }                               
    });
    
    var $ga_visits = $('#ga_visits'),
        $ga_visitors = $('#ga_visitors'),
        $ga_pageviews = $('#ga_pageviews'),
        $ga_pageviewspervisit = $('#ga_pageviewspervisit'),
        $top_referrals = $('#top_referrals'),
        $top_keywords = $('#top_keywords');
        
    load_dashboard();
    
    function load_dashboard(){
        
        chart.showLoading();
        
        $.getJSON('/vendor/google_analytics/data.php', function(res){
            var xAxisLabels     = new Array(),
                pageviews      = new Array(),
                visits         = new Array();  

            $ga_visits.html(res['info']['visits']);
            $ga_visitors.html(res['info']['visitors']);
            $ga_pageviews.html(res['info']['pageviews']);
            $ga_pageviewspervisit.html(res['info']['pageviewsPerVisit']);
            
            var referrals = '';
            $.each(res['referrals'], function(i,v){
                referrals += '<tr><td width="85%"><span class="text-primary">'+v[0]+'</span></td><td class="text-bold">'+Highcharts.numberFormat(v[1], 0, ".", ".")+'</td></tr>';
            });
            $top_referrals.html(referrals);
                  
            var keywords = '';
            $.each(res['keywords'], function(i,v){
                keywords += '<tr><td width="85%"><span class="text-primary">'+v[0]+'</span></td><td class="text-bold">'+Highcharts.numberFormat(v[1], 0, ".", ".")+'</td></tr>';
            });
            $top_keywords.html(keywords);
                                    
            $.each(res['chart'], function(i,v){
                xAxisLabels.push(v["date"]*1000);
                visits.push(parseInt(v["visits"]));                
                pageviews.push(parseInt(v["pageviews"]));                
            });
            
            chart.xAxis[0].setCategories(xAxisLabels);      
            chart.addSeries({
                name: 'Số lượt truy cập', data: visits, type: 'area', lineWidth: 4, 
                marker: { 
                    radius: 5 
                }
            }, false);   
            chart.addSeries({
                name: 'Số trang được xem', data: pageviews, type: 'line', lineWidth: 4, 
                marker: {
                    radius: 5
                }
            }, false);
            
            chart.redraw();    
                      
            chart.hideLoading();
        });
    }       
})();
</script>    

{{ else }}
    <div style="background: url('{{ theme_url('img/analytics.png') }}');width:100%;height:0;padding-bottom:43%;position:relative;z-index:1">
        <div style="background: rgba(255,255,255,0.7);position:absolute;z-index:2;width:100%;height:0;padding-bottom:43%; text-align:center;">
            <div style="margin-top:160px;font-size:42px;font-weight:300;font-family:Roboto,sans-serif;text-shadow: 5px 5px 8px #999;">
                Thống kê lượt truy cập website
                <br>
                <a class="btn btn-primary btn-lg" href="{{ url('config.config') }}/#Google Analytics">Cấu hình Google Analytics</a>
            </div>
        </div>
    </div>
{{ endif }}