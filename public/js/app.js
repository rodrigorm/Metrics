function plot(placeholder, target) {
    var result = function() {
        $.getJSON('/data?target=' + target + '&from=-30min&until=now').done(function(json) {
            var data = []
                , options = {};

            for (var i in json) {
                var time = new Date(json[i].time).getTime();
                var value = parseInt(json[i].value, 10);
                data.push([time, value]);
            }

            options = {
                yaxis: {
                    min: 0
                },
                xaxis: {
                    mode: 'time',
                    timezone: 'browser'
                }
            };

            $(placeholder).plot([data], options);
            setTimeout(result, 5000);
        });
    }

    return result;
}

plot('#placeholder', 'metrics.insert')();
