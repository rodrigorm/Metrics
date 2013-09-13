function val_sum(item) {
    return parseInt(item.sum, 10);
}

function val_count(item) {
    return parseInt(item.count, 10);
}

function val_average(item) {
    return val_sum(item) / val_count(item);
}

val_sum.label = function(target) {
    return 'sum(' + target + ')';
};

val_count.label = function(target) {
    return 'count(' + target + ')';
};

val_average.label = function(target) {
    return 'avg(' + target + ')';
};

function plot(target, calc) {
    var placeholder = $('<div class="chart"></div>');
    placeholder.appendTo($('#dashboard'));

    var result = function() {
        var val = calc || val_average;

        $.getJSON('/data?target=' + target + '&from=-30min&until=now').done(function(json) {
            var data = []
                , options = {};

            for (var i in json) {
                var time = new Date(json[i].time).getTime();
                var value = val(json[i]);
                data.push([time, value]);
            }

            options = {
                yaxis: {
                    min: 0,
                    labelWidth: 20,
                    reserveSpace: true
                },
                xaxis: {
                    mode: 'time',
                    timeformat: '%H:%M',
                    timezone: 'browser',
                    tickSize: [2, 'minute']
                }
            };

            var serie = {
                label: val.label(target),
                data: data
            };
            $(placeholder).plot([serie], options);
            setTimeout(result, 5000);
        });
    }

    return result;
}

plot('metrics.get.elapsed')();
plot('metrics.get.elapsed', val_count)();

plot('metrics.server.elapsed')();
plot('metrics.server.elapsed', val_count)();

plot('metrics.insert.elapsed')();
plot('metrics.insert.elapsed', val_count)();

plot('metrics.insert_message.elapsed')();
plot('metrics.insert_message.elapsed', val_count)();
