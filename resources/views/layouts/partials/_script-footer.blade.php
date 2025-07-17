
<!-- Javascripts -->
<script src="{{ asset('assets/plugins/jquery/jquery-2.2.0.min.js') }}"></script>
<script src="{{ asset('assets/plugins/materialize/js/materialize.min.js') }}"></script>
<script src="{{ asset('assets/plugins/material-preloader/js/materialPreloader.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-blockui/jquery.blockui.js') }}"></script>
<script src="{{ asset('assets/plugins/waypoints/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('assets/plugins/counter-up-master/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('assets/plugins/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/plugins/flot/jquery.flot.min.js') }}"></script>
<script src="{{ asset('assets/plugins/flot/jquery.flot.time.min.js') }}"></script>
<script src="{{ asset('assets/plugins/flot/jquery.flot.symbol.min.js') }}"></script>
<script src="{{ asset('assets/plugins/flot/jquery.flot.resize.min.js') }}"></script>
<script src="{{ asset('assets/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/curvedlines/curvedLines.js') }}"></script>
<script src="{{ asset('assets/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('assets/js/pages/prettify.js') }}"></script>
<script src="{{ asset('assets/js/alpha.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/table-data.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>


<!-- If using ()->important(flash) or flash()->overlay(), you'll need to pull in the JS for Twitter Bootstrap. -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<!-- scripts -->
<script src="{{ asset('assets/plugins/materialize/js/materialize.min.js') }}"></script>

<script>
    $('.qte').on("change", function() {
        var nbr = $('option:selected', this).attr('value');
        if (nbr.length > 0) {

            var valeur = $(".prix_product").find('option:selected').attr('value');
            var nbpv = $(".pointvaleur_id").find('option:selected').attr('title');
            var txt = $(".id_produit").find('option:selected').text();

            txt = txt.split('-');
            $("#idproduit").val(txt[0]).change();

            $("#value").val(valeur * nbr);
            $("#value_pv").val(nbpv * nbr);
        }
    });

    $('.id_produit').on("change", function() {
        var id_pv = $('option:selected', this).attr('title');
        var id_prod = $('option:selected', this).attr('value');
        if (id_prod.length > 0) {

            $(".prix_product").val(id_prod).change();
            $(".pointvaleur_id").val(id_pv).change();
            //alert(id_prod);
        }
    });

    $('#distrib').on("change", function() {

        var nbr = $('option:selected', this).attr('value');
        var url = '{{ route("network.create", ":id") }}';
        url = url.replace(':id', nbr);
        window.location.replace(url);
    });
/*
    document.onreadystatechange = function() {
        if (document.readyState !== "complete") {
            document.querySelector("body").style.visibility = "hidden";
            document.querySelector("#loader").style.visibility = "visible";
        } else {
            document.querySelector("#loader").style.display = "none";
            document.querySelector("body").style.visibility = "visible";
        }
    });
        //$('.main_content_div').load('/details/');

        if (nbr.length > 0) {

            var valeur = $(".prix_product").find('option:selected').attr('value');
            var nbpv = $(".numbers").find('option:selected').attr('title');
            var txt = $(".id_produit").find('option:selected').text();

            txt = txt.split('-');
            $("#idproduit").val(txt[0]).change();

            $("#value").val(valeur * nbr);
            $("#value_pv").val(nbpv * nbr);
        }
    });
*/
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);

    $('.achats-delete').on('click', function() {
        let form_id = $(this).data('form-id');
        let form_product = $(this).data('form-product');
        swal({
                title: "Supprimer ? ",
                text: "la suppression sera définitive !",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $('#' + form_id).submit();
                }
            });
    })
</script>
