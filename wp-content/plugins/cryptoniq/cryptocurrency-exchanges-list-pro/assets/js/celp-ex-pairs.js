(function ($) {
    'use strict';
    $.fn.celpPairstable = function () {
        var $celp_table = $(this);
        var defaultLogo = $celp_table.parents('.currecies-pairs').data('default-logo');
        var prevLbl = $celp_table.data("prev");
        var nextLbl = $celp_table.data("next");
        var showEntriesLbl = $celp_table.data("show-entries");
        var searchLbl = $celp_table.data("search");
        var loadingText = $celp_table.data("loading_records");
        var zeroRecords = $celp_table.data("zero-records");
        var ShowingEntries = $celp_table.data("showing_entries");
        var FilterEntries = $celp_table.data("filter_entries");
        var exId = $celp_table.data("ex-id");
        var coin_symbol = $celp_table.data("coin-symbol");
        var coin_price = $celp_table.data("coin-price");
        var perPage = $celp_table.data("per-page");
        var columns = [];
        $celp_table.find('thead th').each(function (index) {
            var index = $(this).data('index');
            var thisTH = $(this);
            var classes = $(this).data('classes');
            var fiatSymbol = "$";
            columns.push({
                data: index,
                name: index,
                render: function (data, type, row, meta) {
                    if (meta.settings.json === undefined) {
                        return data
                    }
                    if (type === 'display') {
                        switch (index) {
                            case 'id':
                                return row.id;
                                break;
                            case 'pair':
                                return data;
                                break;
                            case 'coin_name':
                                var singleUrl = thisTH.data('coin-single-slug');
                                var url = singleUrl + '/' + row.coin_symbol + '/' + row.coin_id+"/";
                                var html;
                                if (singleUrl != !1) {
                                    html = '<div class="'+classes+'">  <a title ="'+data+'" href = "'+url+'" >   <span class="celp_ex_name">'+data+'</span>  </a></div>'
                                } else {
                                    html = '<div class="'+classes+'"><span class="celp_ex_name">'+data+'</span></div>'
                                }
                                return html;
                            case 'price':
                                if (typeof data !== 'undefined' && data != null) {
                                    data = coin_price * data;
                                    if (data < 0.50) {
                                        var formatedVal = numeral(data).format('0,0.000000')
                                    } else {
                                        var formatedVal = numeral(data).format('0,0.00')
                                    }
                                    return html = '<div data-val="'+row.price+'" class="'+classes+'"><span class="cmc-formatted-price">'+coin_symbol + formatedVal+'</span></div>'
                                } else {
                                    return html = '<div class="'+classes+'">?</div>'
                                }
                                break
                            case 'volume_24h':
                                data = coin_price * data;
                                if (data < 0.50) {
                                    var formatedVal = numeral(data).format('0,0.000000')
                                } else {
                                    var formatedVal = numeral(data).format('0,0.00')
                                }
                                if (typeof data !== 'undefined' && data != null) {
                                    return html = '<div data-val="'+row.usd_volume+'" class="'+classes+'">'+coin_symbol + formatedVal.toUpperCase()+'</div>'
                                } else {
                                    return html = '<div class="'+classes+'">?</span></div>'
                                }
                                break;
                            case 'updated':
                                var html = '<div class="'+classes+'">'+data+'</div>';
                                return html;
                                break
                        }
                    }
                    return data
                },
            })
        });
        $celp_table.DataTable({
            "deferRender": !0,
            "ajax": {
                "url": ajax_object.ajax_url,
                "type": "GET",
                "dataType": "JSON",
                "async": !0,
                "data": function (d) {
                    d.action = "celp_get_pairs_list", d.ex_id = exId
                },
                "error": function (xhr, error, thrown) {
                    //alert('Something wrong with Server')
                }
            },
            "columns": columns,
            "ordering": !0,
            "searching": !0,
            "pageLength": $(this).data('per-page'),
            "pagingType": "simple",
            "renderer": {
                "header": "bootstrap",
            },
            "processing": !0,
            "drawCallback": function (settings) {
                $celp_table.tableHeadFixer({
                    left: 2,
                    'z-index': 1
                })
            },
            "language": {
                "info": ShowingEntries,
                "infoFiltered": '(' + FilterEntries + ')',
                "loadingRecords": loadingText + '...',
                "paginate": {
                    "next": nextLbl,
                    "previous": prevLbl
                },
                "lengthMenu": showEntriesLbl,
                "search": searchLbl,
                "zeroRecords": zeroRecords,
            }
        })
    }
    $(document).ready(function () {
        $("#celp_currency_pairs").celpPairstable()
    })
})(jQuery)