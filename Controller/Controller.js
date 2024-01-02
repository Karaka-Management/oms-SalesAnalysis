import { jsOMS }      from '../../../jsOMS/Utils/oLib.js';
import { Autoloader } from '../../../jsOMS/Autoloader.js';

Autoloader.defineNamespace('omsApp.Modules');

omsApp.Modules.SalesAnalysis = class {
    /**
     * @constructor
     *
     * @since 1.0.0
     */
    constructor  (app)
    {
        this.app = app;
    };

    bind (id)
    {
        const charts = typeof id === 'undefined' ? document.getElementsByTagName('canvas') : [document.getElementById(id)];
        let length   = charts.length;

        for (let i = 0; i < length; ++i) {
            if (charts[i].getAttribute('data-chart') === null
                && charts[i].getAttribute('data-chart') !== 'undefined'
            ) {
                continue;
            }

            this.bindChart(charts[i]);
        }
    };

    bindChart (chart)
    {
        if (typeof chart === 'undefined' || !chart) {
            jsOMS.Log.Logger.instance.error('Invalid chart: ' + chart, 'ClientManagement');

            return;
        }

        const self = this;
        const data = JSON.parse(chart.getAttribute('data-chart'));

        if (data.type === 'choropleth') {
            const parts    = data.mapurl.split('/');
            const fileName = parts[parts.length - 1];
            const mapName  = fileName.replace('.topo.json', '');

            fetch(data.mapurl).then((r) => r.json()).then((d) => {
                const countries = ChartGeo.topojson.feature(d, d.objects[mapName]).features;

                data.data.labels = countries.map((c) => c.properties.name);

                const vals   = {};
                const length = data.data.datasets[0].data.length;
                for (let i = 0; i < length; ++i) {
                    vals[data.data.datasets[0].data[i].id] = data.data.datasets[0].data[i].value;
                }

                data.data.datasets[0].data = countries.map((c) => (
                    {feature: c, value: (vals.hasOwnProperty(c.id) ? vals[c.id] : null)}
                ));

                const myChart = new Chart(chart.getContext('2d'), data);
            });
        } else {
            const myChart = new Chart(chart.getContext('2d'), data);
        }

    };
};

window.omsApp.moduleManager.get('SalesAnalysis').bind();
