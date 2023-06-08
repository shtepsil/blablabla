<? if (isset($tabs)): ?>
    <style>
        .wrap-debug {
            min-height: 400px;
            /*font-size: 16px;*/
        }

        .wrap-debug .dn {
            display: none;
        }

        .wrap-debug .tabs {
            display: flex;
            flex-direction: column;
        }

        .wrap-debug .tabs__links {
            display: flex;
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 10px;
            order: 0;
            white-space: nowrap;
            background-color: #fff;
            border: 1px solid #e3f2fd;
            box-shadow: 0 2px 4px 0 #e3f2fd;
        }

        .wrap-debug .tabs__links>a {
            display: inline-block;
            text-decoration: none;
            padding: 6px 10px;
            text-align: center;
            color: #1976d2;
        }

        .wrap-debug .tabs__links>a:hover {
            background-color: rgba(227, 242, 253, 0.3);
        }

        /* отобразить контент, связанный с вабранной радиокнопкой (input type="radio") */
        <?
        $selectors = '';
        foreach ($tabs as $tab_index => $tab) {
            $selectors .= '.wrap-debug .tabs>#content-' . $tab_index . ':target~.tabs__links>a[href="#content-' . $tab_index . '"],';
        }
        $selectors = substr($selectors, 0, -1);
        ?>
        <?= $selectors ?>
            {
            background-color: #bbdefb;
            cursor: default;
        }

        /*.wrap-debug .tabs>#content-1:target~.tabs__links>a[href="#content-1"],*/
        /*.wrap-debug .tabs>#content-2:target~.tabs__links>a[href="#content-2"],*/
        /*.wrap-debug .tabs>#content-3:target~.tabs__links>a[href="#content-3"] {*/
        /*background-color: #bbdefb;*/
        /*cursor: default;*/
        /*}*/

        .wrap-debug .tabs>div:not(.tabs__links) {
            display: none;
            order: 1;
        }

        .wrap-debug .tabs>div:target {
            display: block;
        }

        .wrap-debug img.loading {
            display: none;
            width: 22px;
            position: absolute;
            top: -77px;
        }

        .wrap-debug button,
        [class=*btn] {
            border: 0;
            cursor: pointer;
        }

        .wrap-debug .btn_debug {
            display: inline;
            font: 1.4rem/1.43em "Proxima Nova", sans-serif;
            padding: 8px 22px;
            overflow: hidden;
            -webkit-border-radius: 20px;
            -moz-border-radius: 20px;
            -o-border-radius: 20px;
            -ms-border-radius: 20px;
            -khtml-border-radius: 20px;
            border-radius: 20px;
        }

        .wrap-debug .btn_debug.blue {
            background-color: #4686cc;
            color: #fff;
        }

        .wrap-debug .btn_debug.error {
            background-color: #CC4646;
            color: #fff;
        }

        .wrap-debug input,
        .wrap-debug textarea {
            width: 100%;
            height: 40px;
            padding-left: 12px;
            padding-right: 12px;
            font: .8em/1.5em "Proxima Nova", sans-serif;
            color: #343332;
            border: 1px solid #d8d7d7;
            overflow: hidden;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -o-border-radius: 3px;
            -ms-border-radius: 3px;
            -khtml-border-radius: 3px;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .wrap-debug .w120 {
            width: 120px;
        }

        .wrap-debug .w150 {
            width: 150px;
        }

        .wrap-debug .w250 {
            width: 250px;
        }

        .wrap-debug .w350 {
            width: 350px;
        }

        .wrap-debug .h90 {
            height: 90px;
        }
    </style>
<? endif ?>