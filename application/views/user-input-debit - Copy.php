<?php require('header.php') ?>
<body>
	<div class="container">
        <a href="<?php echo site_url(); ?>/home/dashboard">
            <img src="<?php echo base_url(); ?>/assets/images/logo-itdc.png" class="logo-itdc">
        </a>
        <div class="row" style="text-align:center">
            <h3 style="margin-top:10px;">INPUT PAJAK MASUKKAN</h3>
            <div class="col-md-12 alert alert-info box-input">
                <p>Masukkan <strong>file .csv</strong> dari pajak</p>
                <?php echo form_open_multipart('faktur/inputdebit');?>                
                    <div class="input-section">
                        <div class="form-control">
                            <input type="text" name="form-file-csv" class="" id="form-file-csv" readonly="true" placeholder="filename....">
                            <span id="file-name"></span>
                            <label class="btn btn-default upload-file-btn">
                                <i class="fa fa-file-o" aria-hidden="true"></i>
                                <input type="file" name="file-csv" hidden="true" id="input-csv">
                            </label>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary" value="posting">
                </form>
            </div>
        </div>
        <p>Jumlah faktur masuk : <strong id="faktur-masuk"></strong></p>
        <p>Jumlah PPN : <strong id="jumlah-ppn"></strong></p>
        <p>Jumlah PPnBM : <strong id="jumlah-ppnbm"></strong></p>
        <?php if (isset($sukses)) {
            ?>
            <div class="alert alert-success message" role="alert">
                <strong>Berhasil ! <?php echo $sukses ?> Faktur </strong> berhasil didebit.
            </div>
            <?php
        } ?>
        <?php if (isset($gagalDebit)) {
            ?>
            <div class="alert alert-danger message" role="alert">
                <strong>Oh snap!</strong> Faktur-faktur berikut <strong>sudah pernah</strong> di debit sebelumnya.
                <p>Total Gagal : <strong><?php echo count($gagalDebit);?></strong> faktur </p>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="failed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>FM</th>
                            <th>KD_JENIS_TRANSAKSI</th>
                            <th>FG_PENGGANTI</th>
                            <th>NOMOR_FAKTUR</th>
                            <th>MASA_PAJAK</th>
                            <th>TAHUN_PAJAK</th>
                            <th>TANGGAL_FAKTUR</th>
                            <th>NPWP</th>
                            <th>NAMA</th>
                            <th>ALAMAT_LENGKAP</th>
                            <th>JUMLAH_DPP</th>
                            <th>JUMLAH_PPN</th>
                            <th>JUMLAH_PPNBM</th>
                            <th>IS_CREDITABLE</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $count = 0;
                    while ($count < count($gagalDebit)) {
                        ?>
                        <tr class="danger">
                            <td><?php echo ++$count;?></td>
                            <td><?php echo $gagalDebit[$count-1]['FM']?></td>
                            <td><?php echo $gagalDebit[$count-1]['KD_JENIS']?></td>
                            <td><?php echo $gagalDebit[$count-1]['FG_PENGGANTI']?></td>
                            <td><?php echo $gagalDebit[$count-1]['NOMOR_FAKTUR']?></td>
                            <td><?php echo $gagalDebit[$count-1]['MASA_PAJAK']?></td>
                            <td><?php echo $gagalDebit[$count-1]['TAHUN_PAJAK']?></td>
                            <td><?php echo $gagalDebit[$count-1]['TANGGAL_FAKTUR']?></td>
                            <td><?php echo $gagalDebit[$count-1]['NPWP']?></td>
                            <td><?php echo $gagalDebit[$count-1]['NAMA']?></td>
                            <td><?php echo $gagalDebit[$count-1]['ALAMAT_LENGKAP']?></td>
                            <td><?php echo $gagalDebit[$count-1]['JUMLAH_DPP']?></td>
                            <td><?php echo $gagalDebit[$count-1]['JUMLAH_PPN']?></td>
                            <td><?php echo $gagalDebit[$count-1]['JUMLAH_PPNBM']?></td>
                            <td><?php echo $gagalDebit[$count-1]['IS_CREDITABLE']?></td>
                        </tr>
                        <?php
                    };?>
                    </tbody>
                </table>
            </div>
            <?php
        } ?>
        <div class="table-responsive">
            <table class="table table-bordered" id="table">
            </table>
        </div>
    </div>
    <?php require('user-action.php') ?>
</body>
<?php require('footer.php') ?>
<script type="text/javascript">
var ppnCount = 0;
var ppnBmCount = 0;

function hitungPpn(nilaiPpn) {
    ppnCount += nilaiPpn;
    return false;
}

function hitungPpnBm(nilaiPpnBm) {
    ppnBmCount += nilaiPpnBm;
    return false;
}

function toRp(angka) {
    var rev = parseInt(angka, 10).toString().split('').reverse().join('');
    var rev2 = '';
    for (var i = 0; i < rev.length; i++) {
        rev2 += rev[i];
        if ((i + 1) % 3 === 0 && i !== (rev.length - 1)) {
            rev2 += '.';
        }
    }
    return 'Rp. ' + rev2.split('').reverse().join('') + ',00';
}

var arrayFaktur = [];

$('#input-csv').change(function(e) {
    var ext = $('#input-csv').val().split(".").pop().toLowerCase();
    if ($.inArray(ext, ["csv"]) == -1) {
        alert('Upload CSV');
        return false;
    } else {
        $('#form-file-csv').val($('#input-csv').val().split("\\").pop());
    }

    if (e.target.files != undefined) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var table = "<thead><tr><th><div class='dropdown'><button class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>Check<span class='caret'></span></button><ul class='dropdown-menu'>  <li><a href='#'>Check all</a></li>  <li><a href='#'>Uncheck all</a></li> </ul></div></th><th>FM</th><th>KD_JENIS_TRANSAKSI</th><th>FG_PENGGANTI</th><th>NOMOR_FAKTUR</th><th>MASA_PAJAK</th><th>TAHUN_PAJAK</th><th>TANGGAL_FAKTUR</th><th>NPWP</th><th>NAMA</th><th>ALAMAT_LENGKAP</th><th>JUMLAH_DPP</th><th>JUMLAH_PPN</th><th>JUMLAH_PPNBM</th><th>IS_CREDITABLE</th><th></tr></thead><tbody>";
            var csvval = e.target.result.split("\n");
            // console.log(csvval[1]);
            for (var i = 1; i < csvval.length; i++) {
                var row = "<tr>";
                row = row + "<td>" + i + "</td>";
                arrayFaktur.push({'isChecked':false,'isi':[]});
                var fakturValue = csvval[i].split('","');
                for (var j = 0; j < fakturValue.length; j++) {
                    row = row + "<td>" + fakturValue[j].replace('"', '') + "</td>";
                    arrayFaktur[i-1].isi.push(fakturValue[j].replace('"', ''));
                    if (j == 11) {
                        hitungPpn(parseInt(fakturValue[j].replace('"', '')));
                    } else if (j == 12) {
                        hitungPpnBm(parseInt(fakturValue[j].replace('"', '')));
                    }
                }
                table = table + row + "</tr>";
            }
            console.log(arrayFaktur);
            table = table + "</tbody>";
            $("#faktur-masuk").text(csvval.length - 1);
            $("#jumlah-ppn").text(toRp(ppnCount));
            $("#jumlah-ppnbm").text(toRp(ppnBmCount));
            $('#table').append(table);
            $('.message').remove();
            $('#failed').remove();
        };
        reader.readAsText(e.target.files.item(0));
    }

    return false;
});

$('#form-file-csv').click(function(e) {
    e.preventDefault();
    $('#input-csv').click();
    return false;
})
</script>