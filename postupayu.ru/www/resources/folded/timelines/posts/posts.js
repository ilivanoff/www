$(function() {
    PsTimeLine.create({
        ctxt: null,
        div: 'TimeLinePosts',
        bands: [{
            width:          "70%",
            intervalUnit:   PsTimeLine.DateTime.DAY,
            intervalPixels: 100
        },{
            overview:       true,
            width:          "15%",
            intervalUnit:   PsTimeLine.DateTime.WEEK,
            intervalPixels: 200
        },{
            overview:       true,
            width:          "15%",
            intervalUnit:   PsTimeLine.DateTime.MONTH,
            intervalPixels: 200
        }],
        data: {
            lident: 'posts'
        }
    });
});