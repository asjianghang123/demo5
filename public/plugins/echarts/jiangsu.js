(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['exports', 'echarts'], factory);
    } else if (typeof exports === 'object' && typeof exports.nodeName !== 'string') {
        // CommonJS
        factory(exports, require('echarts'));
    } else {
        // Browser globals
        factory({}, root.echarts);
    }
}(this, function (exports, echarts) {
    var log = function (msg) {
        if (typeof console !== 'undefined') {
            console && console.error && console.error(msg);
        }
    }
    if (!echarts) {
        log('ECharts is not Loaded');
        return;
    }
    if (!echarts.registerMap) {
        log('ECharts Map is not loaded')
        return;
    }
    echarts.registerMap('江苏', {
        "type": "FeatureCollection",
        "cp": [118.8586, 32.915],
        "size": "1950",
        "features":
            [
                { "type": "Feature", "properties": { "id": "3206", "name": "南通市", "cp": [121.1023, 32.1625], "childNum": 7 }, "geometry": { "type": "Polygon", "coordinates": [[[120.2014, 32.6019], [120.2124, 32.6074], [120.2124, 32.6239], [120.2344, 32.6514], [120.2234, 32.6733], [120.2783, 32.6788], [120.3113, 32.7063], [120.3333, 32.6953], [120.3333, 32.7008], [120.3772, 32.7118], [120.3882, 32.7063], [120.3882, 32.6953], [120.4321, 32.6788], [120.4431, 32.6294], [120.4761, 32.6294], [120.4871, 32.6239], [120.498, 32.6294], [120.498, 32.6349], [120.509, 32.6404], [120.542, 32.6349], [120.575, 32.6349], [120.5969, 32.6514], [120.6189, 32.6239], [120.6189, 32.6074], [120.6299, 32.6184], [120.6519, 32.6019], [120.6519, 32.5854], [120.6628, 32.5745], [120.6848, 32.5964], [120.7068, 32.6019], [120.7178, 32.6019], [120.7178, 32.5909], [120.7288, 32.5854], [120.7507, 32.6019], [120.7947, 32.6019], [120.7947, 32.58], [120.8276, 32.5909], [120.8386, 32.6074], [120.8496, 32.6074], [120.8826, 32.6239], [120.9924, 32.6569], [121.0144, 32.6349], [121.1133, 32.569], [121.1792, 32.536], [121.4099, 32.4811], [121.4539, 32.4536], [121.4758, 32.3492], [121.4868, 32.2504], [121.4978, 32.157], [121.5417, 32.146], [121.7065, 32.135], [121.8164, 32.1021], [121.9592, 31.9922], [121.9812, 31.9537], [121.9922, 31.8823], [122.0032, 31.6681], [122.0032, 31.6132], [121.8713, 31.6187], [121.5637, 31.723], [121.5088, 31.745], [121.4429, 31.7615], [121.366, 31.8384], [121.311, 31.8658], [121.2891, 31.8658], [121.2231, 31.8439], [121.1023, 31.7615], [121.0583, 31.7834], [121.0034, 31.7834], [120.8936, 31.8219], [120.7837, 32.0032], [120.575, 32.0032], [120.553, 32.0142], [120.564, 32.0361], [120.553, 32.0526], [120.52, 32.0581], [120.52, 32.0856], [120.509, 32.0911], [120.4431, 32.124], [120.3552, 32.1295], [120.3442, 32.168], [120.3662, 32.2174], [120.3662, 32.2394], [120.3442, 32.2504], [120.3552, 32.3053], [120.3662, 32.3108], [120.3442, 32.3383], [120.3552, 32.3547], [120.3552, 32.3767], [120.3113, 32.3602], [120.3003, 32.3712], [120.2893, 32.3657], [120.2783, 32.4701], [120.2563, 32.4921], [120.2673, 32.5031], [120.2673, 32.5525], [120.2563, 32.5964], [120.2234, 32.5854], [120.2014, 32.6019]]] } },
                { "type": "Feature", "properties": { "id": "3205", "name": "苏州市", "cp": [120.6519, 31.3989], "childNum": 6 }, "geometry": { "type": "Polygon", "coordinates": [[[119.9158, 31.1682], [120.0037, 31.2506], [120.1025, 31.2616], [120.1245, 31.2726], [120.1794, 31.322], [120.2344, 31.355], [120.2563, 31.3824], [120.4211, 31.4484], [120.498, 31.4484], [120.553, 31.4758], [120.553, 31.4813], [120.553, 31.4978], [120.553, 31.5088], [120.5859, 31.5253], [120.6079, 31.5198], [120.5969, 31.5857], [120.553, 31.5747], [120.542, 31.5857], [120.542, 31.6022], [120.564, 31.6022], [120.575, 31.6132], [120.5969, 31.6187], [120.5969, 31.6241], [120.5969, 31.6516], [120.564, 31.6571], [120.564, 31.6681], [120.564, 31.6846], [120.5859, 31.6901], [120.5969, 31.712], [120.5859, 31.712], [120.5859, 31.7285], [120.5969, 31.745], [120.5859, 31.778], [120.564, 31.7889], [120.531, 31.7889], [120.52, 31.8054], [120.531, 31.8329], [120.498, 31.8439], [120.4871, 31.8713], [120.4651, 31.8823], [120.4651, 31.8878], [120.3772, 31.9153], [120.3882, 31.9318], [120.3772, 31.9427], [120.3662, 31.9867], [120.4211, 32.0306], [120.4321, 32.0361], [120.553, 32.0142], [120.575, 32.0032], [120.7837, 32.0032], [120.8936, 31.8219], [121.0034, 31.7834], [121.0583, 31.7834], [121.1023, 31.7615], [121.2671, 31.6461], [121.322, 31.5857], [121.3879, 31.5472], [121.322, 31.4978], [121.311, 31.5033], [121.3, 31.4923], [121.2451, 31.4758], [121.2451, 31.4923], [121.2341, 31.4923], [121.1792, 31.4539], [121.1462, 31.4429], [121.1462, 31.4374], [121.1682, 31.4319], [121.1462, 31.4209], [121.1572, 31.4099], [121.1462, 31.3879], [121.1023, 31.366], [121.1133, 31.355], [121.1243, 31.344], [121.1243, 31.3055], [121.1462, 31.3], [121.1572, 31.2836], [121.1462, 31.2726], [121.1133, 31.2836], [121.1023, 31.2781], [121.0913, 31.2946], [121.0803, 31.2726], [121.0693, 31.2671], [121.0583, 31.2177], [121.0803, 31.1682], [121.0693, 31.1517], [121.0474, 31.1572], [121.0364, 31.1353], [121.0254, 31.1407], [120.9814, 31.1353], [120.9265, 31.1407], [120.8826, 31.1353], [120.8606, 31.1023], [120.8716, 31.0968], [120.8936, 31.0968], [120.9045, 31.0803], [120.8936, 31.0474], [120.9045, 31.0364], [120.8936, 31.0034], [120.8496, 30.9924], [120.8276, 31.0034], [120.8057, 31.0034], [120.7727, 30.9924], [120.7727, 30.976], [120.7507, 30.965], [120.7288, 30.9705], [120.6958, 30.9705], [120.6848, 30.9595], [120.7068, 30.932], [120.7178, 30.8826], [120.7068, 30.8826], [120.7068, 30.8716], [120.6848, 30.8826], [120.6628, 30.8606], [120.6628, 30.8661], [120.6519, 30.8496], [120.6299, 30.8551], [120.5859, 30.8551], [120.564, 30.8331], [120.509, 30.7562], [120.4871, 30.7617], [120.4761, 30.7837], [120.4761, 30.8057], [120.4541, 30.8167], [120.4651, 30.8386], [120.4431, 30.8551], [120.4541, 30.8661], [120.4321, 30.8881], [120.4321, 30.921], [120.4211, 30.9265], [120.4211, 30.8936], [120.3662, 30.8826], [120.3552, 30.91], [120.3662, 30.9485], [120.3113, 30.932], [120.2563, 30.9265], [120.1245, 30.943], [120.0586, 31.0034], [119.9927, 31.0309], [119.9817, 31.0583], [119.9487, 31.1023], [119.9377, 31.1407], [119.9268, 31.1627], [119.9158, 31.1682]]] } },
                { "type": "Feature", "properties": { "id": "3202", "name": "无锡市", "cp": [120.3442, 31.5527], "childNum": 3 }, "geometry": { "type": "Polygon", "coordinates": [[[120.1794, 31.7505], [120.2014, 31.756], [120.1685, 31.8274], [120.1904, 31.8549], [120.1794, 31.8658], [120.1575, 31.8713], [120.1135, 31.8549], [120.0916, 31.8549], [120.0476, 31.8219], [120.0256, 31.8329], [120.0146, 31.8219], [120.0037, 31.8274], [119.9927, 31.8549], [120.0037, 31.8658], [120.0146, 31.8823], [120.0037, 31.8933], [120.0037, 31.9098], [120.0146, 31.9153], [120.0037, 31.9482], [120.0256, 31.9702], [120.1794, 31.9263], [120.2563, 31.9373], [120.3662, 31.9867], [120.3772, 31.9427], [120.3882, 31.9318], [120.3772, 31.9153], [120.4651, 31.8878], [120.4651, 31.8823], [120.4871, 31.8713], [120.498, 31.8439], [120.531, 31.8329], [120.52, 31.8054], [120.531, 31.7889], [120.564, 31.7889], [120.5859, 31.778], [120.5969, 31.745], [120.5859, 31.7285], [120.5859, 31.712], [120.5969, 31.712], [120.5859, 31.6901], [120.564, 31.6846], [120.564, 31.6681], [120.564, 31.6571], [120.5969, 31.6516], [120.5969, 31.6241], [120.5969, 31.6187], [120.575, 31.6132], [120.564, 31.6022], [120.542, 31.6022], [120.542, 31.5857], [120.553, 31.5747], [120.5969, 31.5857], [120.6079, 31.5198], [120.5859, 31.5253], [120.553, 31.5088], [120.553, 31.4978], [120.553, 31.4813], [120.553, 31.4758], [120.498, 31.4484], [120.4211, 31.4484], [120.2563, 31.3824], [120.2344, 31.355], [120.1794, 31.322], [120.1245, 31.2726], [120.1025, 31.2616], [120.0037, 31.2506], [119.9158, 31.1682], [119.8828, 31.1627], [119.8279, 31.1737], [119.8059, 31.1627], [119.7949, 31.1572], [119.7949, 31.1682], [119.7729, 31.1792], [119.718, 31.1682], [119.707, 31.1517], [119.6741, 31.1682], [119.6411, 31.1462], [119.6301, 31.1298], [119.5862, 31.1188], [119.5752, 31.1353], [119.5313, 31.1572], [119.5532, 31.1792], [119.5532, 31.1957], [119.5532, 31.2231], [119.5203, 31.2396], [119.5313, 31.2781], [119.5203, 31.3165], [119.5313, 31.333], [119.5313, 31.366], [119.5422, 31.3934], [119.5313, 31.4099], [119.5532, 31.4154], [119.5532, 31.4319], [119.5752, 31.4319], [119.5862, 31.4484], [119.5862, 31.4648], [119.5642, 31.4648], [119.5752, 31.4813], [119.5642, 31.5033], [119.5862, 31.5033], [119.6082, 31.5527], [119.6411, 31.5747], [119.6411, 31.6022], [119.6631, 31.6132], [119.6851, 31.6022], [119.718, 31.5582], [119.729, 31.5637], [119.7949, 31.5527], [119.8389, 31.5308], [119.8499, 31.5308], [119.8608, 31.5472], [119.9377, 31.5527], [119.9707, 31.5363], [119.9927, 31.5033], [120.0146, 31.5033], [120.0366, 31.4978], [120.0476, 31.4868], [120.0476, 31.4703], [120.0366, 31.4264], [120.0256, 31.3879], [120.0256, 31.366], [120.0366, 31.344], [120.0916, 31.333], [120.1025, 31.3385], [120.0916, 31.3495], [120.0366, 31.366], [120.0476, 31.4044], [120.0586, 31.4429], [120.1025, 31.4594], [120.1135, 31.4813], [120.1245, 31.5033], [120.1245, 31.5143], [120.1025, 31.5198], [120.1025, 31.5472], [120.0586, 31.5582], [120.0586, 31.5747], [120.0806, 31.6077], [120.1245, 31.6406], [120.1245, 31.6846], [120.1465, 31.6736], [120.1465, 31.6846], [120.1465, 31.6956], [120.1575, 31.701], [120.1575, 31.756], [120.1685, 31.7615], [120.1794, 31.7505]]] } },
                { "type": "Feature", "properties": { "id": "3204", "name": "常州市", "cp": [119.4543, 31.5582], "childNum": 3 }, "geometry": { "type": "Polygon", "coordinates": [[[119.2346, 31.6296], [119.2676, 31.6351], [119.3115, 31.6736], [119.3335, 31.701], [119.3225, 31.7175], [119.3335, 31.7285], [119.3005, 31.734], [119.3005, 31.7615], [119.3225, 31.7834], [119.3115, 31.8164], [119.3335, 31.8384], [119.3335, 31.8494], [119.3445, 31.8604], [119.3665, 31.8604], [119.3994, 31.8329], [119.4434, 31.8274], [119.4214, 31.8549], [119.4543, 31.8878], [119.5093, 31.8549], [119.5532, 31.8604], [119.5862, 31.8439], [119.6191, 31.7944], [119.6411, 31.7944], [119.6521, 31.7725], [119.696, 31.767], [119.707, 31.7395], [119.74, 31.7285], [119.74, 31.7505], [119.751, 31.7834], [119.762, 31.7889], [119.762, 31.8054], [119.7949, 31.8054], [119.7839, 31.8164], [119.8059, 31.8494], [119.7729, 31.8494], [119.762, 31.8713], [119.751, 31.8823], [119.751, 31.8878], [119.7839, 31.9043], [119.7839, 31.9208], [119.7949, 31.9263], [119.7729, 31.9482], [119.7839, 32.0251], [119.8059, 32.0581], [119.8718, 32.0581], [119.8828, 32.0416], [119.8938, 32.0471], [119.9158, 32.0032], [119.9817, 32.0087], [120.0256, 31.9702], [120.0037, 31.9482], [120.0146, 31.9153], [120.0037, 31.9098], [120.0037, 31.8933], [120.0146, 31.8823], [120.0037, 31.8658], [119.9927, 31.8549], [120.0037, 31.8274], [120.0146, 31.8219], [120.0256, 31.8329], [120.0476, 31.8219], [120.0916, 31.8549], [120.1135, 31.8549], [120.1575, 31.8713], [120.1794, 31.8658], [120.1904, 31.8549], [120.1685, 31.8274], [120.2014, 31.756], [120.1794, 31.7505], [120.1685, 31.7615], [120.1575, 31.756], [120.1575, 31.701], [120.1465, 31.6956], [120.1465, 31.6846], [120.1465, 31.6736], [120.1245, 31.6846], [120.1245, 31.6406], [120.0806, 31.6077], [120.0586, 31.5747], [120.0586, 31.5582], [120.1025, 31.5472], [120.1025, 31.5198], [120.1245, 31.5143], [120.1245, 31.5033], [120.1135, 31.4813], [120.1025, 31.4594], [120.0586, 31.4429], [120.0476, 31.4044], [120.0366, 31.366], [120.0916, 31.3495], [120.1025, 31.3385], [120.0916, 31.333], [120.0366, 31.344], [120.0256, 31.366], [120.0256, 31.3879], [120.0366, 31.4264], [120.0476, 31.4703], [120.0476, 31.4868], [120.0366, 31.4978], [120.0146, 31.5033], [119.9927, 31.5033], [119.9707, 31.5363], [119.9377, 31.5527], [119.8608, 31.5472], [119.8499, 31.5308], [119.8389, 31.5308], [119.7949, 31.5527], [119.729, 31.5637], [119.718, 31.5582], [119.6851, 31.6022], [119.6631, 31.6132], [119.6411, 31.6022], [119.6411, 31.5747], [119.6082, 31.5527], [119.5862, 31.5033], [119.5642, 31.5033], [119.5752, 31.4813], [119.5642, 31.4648], [119.5862, 31.4648], [119.5862, 31.4484], [119.5752, 31.4319], [119.5532, 31.4319], [119.5532, 31.4154], [119.5313, 31.4099], [119.5422, 31.3934], [119.5313, 31.366], [119.5313, 31.333], [119.5203, 31.3165], [119.5313, 31.2781], [119.5203, 31.2396], [119.5532, 31.2231], [119.5532, 31.1957], [119.5532, 31.1792], [119.5313, 31.1572], [119.5093, 31.1572], [119.4873, 31.1627], [119.4873, 31.1517], [119.4543, 31.1572], [119.4324, 31.1792], [119.4214, 31.1737], [119.3884, 31.1737], [119.3884, 31.1847], [119.4104, 31.1902], [119.3994, 31.1957], [119.3774, 31.1902], [119.3665, 31.2067], [119.3665, 31.2396], [119.3774, 31.2671], [119.3555, 31.3], [119.3445, 31.2616], [119.3115, 31.2671], [119.2676, 31.2506], [119.2566, 31.2616], [119.2456, 31.2561], [119.2236, 31.2671], [119.2126, 31.2726], [119.2017, 31.2671], [119.2017, 31.2726], [119.2017, 31.2946], [119.1797, 31.3], [119.1907, 31.311], [119.2017, 31.333], [119.2236, 31.3495], [119.1907, 31.3824], [119.1687, 31.3824], [119.1687, 31.4374], [119.1467, 31.4539], [119.1577, 31.4703], [119.1467, 31.4868], [119.1907, 31.5033], [119.2017, 31.5198], [119.1797, 31.5253], [119.1797, 31.5363], [119.1907, 31.5527], [119.2017, 31.5527], [119.2126, 31.5692], [119.2346, 31.6296]]] } },
                { "type": "Feature", "properties": { "id": "3211", "name": "镇江市", "cp": [119.4763, 31.9702], "childNum": 4 }, "geometry": { "type": "Polygon", "coordinates": [[[119.2346, 32.2229], [119.2786, 32.2339], [119.3774, 32.2284], [119.4214, 32.2504], [119.4543, 32.2614], [119.5203, 32.2504], [119.5422, 32.2833], [119.5752, 32.2888], [119.5752, 32.2778], [119.5642, 32.2723], [119.5752, 32.2504], [119.5862, 32.2559], [119.6082, 32.2504], [119.6191, 32.2229], [119.6301, 32.2559], [119.6741, 32.2668], [119.696, 32.2833], [119.707, 32.2778], [119.74, 32.3108], [119.762, 32.3163], [119.8279, 32.2998], [119.8718, 32.2668], [119.9048, 32.146], [119.9268, 32.0966], [119.9817, 32.0087], [119.9158, 32.0032], [119.8938, 32.0471], [119.8828, 32.0416], [119.8718, 32.0581], [119.8059, 32.0581], [119.7839, 32.0251], [119.7729, 31.9482], [119.7949, 31.9263], [119.7839, 31.9208], [119.7839, 31.9043], [119.751, 31.8878], [119.751, 31.8823], [119.762, 31.8713], [119.7729, 31.8494], [119.8059, 31.8494], [119.7839, 31.8164], [119.7949, 31.8054], [119.762, 31.8054], [119.762, 31.7889], [119.751, 31.7834], [119.74, 31.7505], [119.74, 31.7285], [119.707, 31.7395], [119.696, 31.767], [119.6521, 31.7725], [119.6411, 31.7944], [119.6191, 31.7944], [119.5862, 31.8439], [119.5532, 31.8604], [119.5093, 31.8549], [119.4543, 31.8878], [119.4214, 31.8549], [119.4434, 31.8274], [119.3994, 31.8329], [119.3665, 31.8604], [119.3445, 31.8604], [119.3335, 31.8494], [119.3335, 31.8384], [119.3115, 31.8164], [119.3225, 31.7834], [119.3005, 31.7615], [119.3005, 31.734], [119.3335, 31.7285], [119.3225, 31.7175], [119.3335, 31.701], [119.3115, 31.6736], [119.2676, 31.6351], [119.2346, 31.6296], [119.2126, 31.6296], [119.2017, 31.6461], [119.1907, 31.6516], [119.1907, 31.6901], [119.1577, 31.701], [119.1357, 31.723], [119.0808, 31.7834], [119.0479, 31.7889], [119.0149, 31.778], [119.0039, 31.778], [118.9929, 31.767], [118.9819, 31.767], [118.9819, 31.7834], [119.0039, 31.7834], [119.0039, 31.7944], [118.9709, 31.8329], [118.9819, 31.8439], [119.0259, 31.8494], [119.0698, 31.8713], [119.0918, 31.8604], [119.1248, 31.8933], [119.1028, 31.9318], [119.0479, 31.9373], [119.0259, 31.9537], [119.0369, 31.9647], [119.0698, 31.9757], [119.0918, 31.9757], [119.1028, 31.9647], [119.1248, 31.9812], [119.1138, 32.0032], [119.0918, 32.0032], [119.1028, 32.0142], [119.0918, 32.0526], [119.1028, 32.0691], [119.1028, 32.0911], [119.0918, 32.0911], [119.0808, 32.1075], [119.0479, 32.1021], [119.0369, 32.113], [119.0039, 32.113], [119.0149, 32.124], [119.0479, 32.157], [119.0698, 32.1625], [119.0808, 32.179], [119.1357, 32.1954], [119.1577, 32.1844], [119.2126, 32.1899], [119.2346, 32.2119], [119.2346, 32.2229]]] } }
            ]
    });
}));