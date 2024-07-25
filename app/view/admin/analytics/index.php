<style>
    .empty_state {
        position: relative;
        top: -20px;
        width: 100%;
        display: flex;
        flex-direction: column;
        margin-bottom: 50px;
        margin-top: 100px;
    }

    .empty_state i {
        margin: auto;
        margin-bottom: 0px;
        font-size: 90px;
        color: #ccc;
    }

    .empty_state h3 {
        margin: 8px 0px;
        text-align: center;
        font-weight: normal;
    }

    .empty_state p {
        font-size: 14px;
        margin: 0px;
        color: #999;
        text-align: center;
    }

    .empty_state button {
        outline: none;
        border: none;
        border-radius: 3px;
        padding: 8px 8px;
        margin: 20px auto auto auto;
        width: 50%;
        max-width: 200px;
        background: #348AC7;
        color: white;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        font-size: 12px;
    }

    table {
        border-collapse: collapse;
        overflow: hidden;
        cursor: pointer;
    }

    tbody tr:hover {
        background-color: #f2eeeb;
        z-index: 22;
    }

    td:not(.first):hover::after,
    th:hover::after {
        position: absolute;
        content: "";
        width: 100%;
        left: 0;
        background-color: #f2eeeb;
        z-index: -1;
        /*   background: -webkit-linear-gradient( top,#ccc,#ccc); */
    }
</style>

<div id="page-content">
    <ul class="breadcrumb breadcrumb-top">
        <li>Admin</li>
        <li>Analytics</li>
    </ul>
    <div class="block full">
        <div class="block-title">
            <h2><strong>Analytics</strong></h2>
        </div>
        <div class="row">
            <form action="<?= base_url("a/analytics/") ?>" method="GET">
                <div class="col-md-9">
                    <!-- <div class="col-md-3" style="margin-top: 1em;">
                        <label for="corner">Corner</label>
                        <select id="corner" class="form-control">
                            <option value="">--------PILIH-----------</option>
                            <?php
                            $data = ["Home", "Buy&Sell", "Community", "My", "MainBanner", "SideMenuBar", "Chat", "GNB", "Wallet"];
                            sort($data);
                            foreach ($data as $val) {
                                echo '<option value="' . $val . '" > ' . ucwords($val) . ' ';
                            }
                            ?>
                        </select>
                    </div> -->

                    <div class="col-md-3" style="margin-top: 1em;">
                        <div class="form-group">
                            <label for="">From Date</label>
                            <input id="from_date" name="from_date" type="text" class="form-control input-datepicker" <?php if (isset($_GET['submit'])) { ?> value="<?php echo $_GET['from_date'] ?>" <?php } ?> data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top: 1em;">
                        <div class="form-group">
                            <label for="">To Date</label>
                            <input id="to_date" name="to_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" <?php if (isset($_GET['submit'])) { ?> value="<?php echo $_GET['to_date'] ?>" <?php } ?> placeholder="To Date" readonly />
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <!-- <div class="col-md-3"> -->
                    <label for="fl_reset">&nbsp;</label>
                    <!-- </div> -->
                    <!-- <div class="col-md-3"> -->
                    <label for="fl_button">&nbsp;</label>
                    <a href="<?= base_url('a/analytics') ?>" class="btn btn-primary btn-block">
                        <i class="fa fa-filter"></i> Reset
                    </a>
                    <label for="fl_button">&nbsp;</label>
                    <button id="fl_button" type="submit" class="btn btn-info btn-block" name="submit" value="filter">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>

                <?php if (count($gsa_list) > 0) { ?>
                    <div class="col-md-9">
                        <table class="table table-borderless" style="width: 60%; font-size: 18px;">
                            <tr>
                                <td>
                                    <p><b>Total View <sup>(With Video)</sup></b></p>
                                </td>
                                <td width="20%">:</td>
                                <td><b><?= number_format($totalView); ?></b></td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Video View</b></p>
                                </td>
                                <td width="20%">:</td>
                                <td><b><?= number_format($videoView) ?></b></td>
                            </tr>
                        </table>
                    </div>
                    <!-- </div> -->
                    <div class="col-md-3">
                        <label for="fl_button">&nbsp;</label>
                        <button id="download-summary" type="button" class="btn btn-success btn-block">
                            <i class="fa fa-file-excel-o"></i>
                            Export Excel (Summary)
                        </button>
                        <!-- <label for="fl_button">&nbsp;</label>
                        <button id="download" type="button" class="btn btn-success btn-block">
                            <i class="fa fa-file-excel-o"></i>
                            Export Excel (Detail)
                        </button> -->
                    </div>
                <?php } ?>
            </form>
        </div>

        <?php if (count($gsa_list) > 0) { ?>
            <div class="table-responsive" style="padding-top: 30px">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th colspan="100" class="text-right" style="padding-right: 40px;">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $prevValueCorner = "";
                        $prevValueType = "";
                        $prev_chat = "";
                        $prev_chat2 = "";
                        $prev_chat3 = "";
                        $prev_chat4 = "";
                        $prev_chat5 = "";
                        foreach ($gsa_list as $key => $row) {
                        ?>
                            <tr data-toggle="collapse" data-target="#<?= $key == 'Buy&Sell' ? 'buyandsell' : $key ?>" class="accordion-toggle arrow">
                                <td width="10%">
                                    <button class="btn btn-default btn-xs">
                                        <span class="fa fa-arrow-right" data-active="no"></span>
                                    </button>
                                </td>
                                <td class="bold text-left">
                                    <b style="font-size: 20px">
                                        <?= $key . ' (' . count($row) . ')'; ?>
                                    </b>
                                </td>
                                <td class="text-right">
                                    <p style="padding-right: 35px; font-size: 18px; font-weight: 600">
                                        <?php
                                        if ($key == "Home") {
                                            echo " - View : $totalViewHome";
                                        } elseif ($key == "Buy&Sell") {
                                            echo "
                                                - View : $subtotalView <br>
                                                - Video: $subtotalViewVideo";
                                        } elseif ($key == 'Community') {
                                            echo "
                                                - View : $subtotalViewComm <br>
                                                - Video : $subtotalViewCommVideo
                                            ";
                                        } elseif ($key == "My") {
                                            echo " - View : $totalViewMy";
                                        } elseif ($key == "Chat") {
                                            echo " - View : $totalViewChat";
                                        } elseif ($key == "Wallet") {
                                            echo " - View : $totalViewWallet";
                                        } elseif ($key == "GNB") {
                                            echo " - View : $totalViewGNB";
                                        } elseif ($key == "MainBanner") {
                                            echo " - View : $totalViewMainBanner";
                                        } elseif ($key == "SideMenuBar") {
                                            echo " - View : $totalViewSMB";
                                        } elseif ($key == "Club") {
                                            echo " - View : $totalViewClub";
                                        } else {
                                            echo "";
                                        }
                                        ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="12" class="hiddenRow">
                                    <div class="accordian-body collapse" id="<?= $key == 'Buy&Sell' ? 'buyandsell' : $key ?>">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Corner</th>
                                                    <th>Type</th>
                                                    <th><?= $key == 'Chat' ? 'RoomId' : 'Category'; ?></th>
                                                    <th>Count</th>
                                                </tr>
                                            <tbody style="font-size: large;">
                                                <?php
                                                foreach ($row as $key2 => $data) {
                                                    foreach ($data as $val) {
                                                ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                if ($key != $prevValueCorner) {
                                                                    echo $key;
                                                                }
                                                                $prevValueCorner = $key;
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($key2 != $prevValueType) {
                                                                    echo $key2 . '<br>';
                                                                }

                                                                $prevValueType = $key2;

                                                                ?>
                                                            </td>
                                                            <td style="text-transform: capitalize;">
                                                                <?php if ($key == 'Chat') : ?>
                                                                    <?php if ($val->type != 'Main') : ?>
                                                                    <?php else : ?>
                                                                        <?= $val->category ?>
                                                                    <?php endif ?>
                                                                <?php else : ?>
                                                                    <?= $val->category ?>
                                                                <?php endif ?>
                                                            </td>
                                                            <td><?= $val->count ?></td>
                                                        </tr>
                                                <?php }
                                                } ?>

                                            </tbody>
                                        </table>

                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="empty_state">
                <i class="ion-sad-outline"></i>
                <h3 class="">No data</h3>
                <p>Please choose from date and to date filter to show data.</p>
            </div>
        <?php } ?>
    </div>
</div>