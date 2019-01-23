<?php

/**
 * ScaleExporter.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Http\Requests\ScaleExportRequest;
use Illuminate\Database\Eloquent\Collection;
use PHPExcel;
use PHPExcel_Chart;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_Title;
use PHPExcel_Writer_Excel2007;

/**
 * 规模概览报告
 * Class ScaleExporter
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ScaleExporter extends Controller
{


    /**
     * 创建Excel报告.
     *
     * @param Request $request HTTP请求
     *
     * @return string Excel文件名
     */
    public function export(ScaleExportRequest $request)
    {
        // 创建excel对象.
        $excel = new PHPExcel();

        // 创建基站类型分布sheet.
        $sheetBSC = $excel->getSheet(0);
        $sheetBSC->setTitle('基站类型分布');

        $bscByType = new BscByType($request);
        $sheetBSC->addChart($bscByType->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20'));
        $sheetBSC->fromArray($bscByType->toExcelArray(), null, 'A21');

        $bscByCA = new BscByCA($request);
        $sheetBSC->addChart($bscByCA->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('O20'));
        $sheetBSC->fromArray($bscByCA->toExcelArray(), null, 'H21');

        $bscByCarrier = new BscByCarrier($request);
        $sheetBSC->addChart($bscByCarrier->toExcelChart()->setTopLeftPosition('P1')->setBottomRightPosition('U20'));
        $sheetBSC->fromArray($bscByCarrier->toExcelArray(), null, 'P21');

        // 创建基站版本分布sheet
        $sheetBSCVersion = $excel->createSheet(1);
        $sheetBSCVersion->setTitle('基站版本分布');

        $bscVersionByCity = new BscVersionByCity($request);
        $sheetBSCVersion->addChart($bscVersionByCity->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        $sheetBSCVersion->fromArray($bscVersionByCity->toExcelArray(), null, 'A21');

        $bscVersionByType = new BscVersionByType($request);
        $sheetBSCVersion->addChart($bscVersionByType->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('O20'));
        $sheetBSCVersion->fromArray($bscVersionByType->toExcelArray(), null, 'H21');

        $sheetCarrier = $excel->createSheet(2);
        $sheetCarrier->setTitle('载频频点分布');

        $carrierByCity = new CarrierByCity($request);
        $sheetCarrier->addChart($carrierByCity->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('L20'));
        $sheetCarrier->fromArray($carrierByCity->toExcelArray(), null, 'A21');

        $carrierByChannel = new CarrierByChannel($request);
        $sheetCarrier->addChart($carrierByChannel->toExcelChart()->setTopLeftPosition('M1')->setBottomRightPosition('Q20'));
        $sheetCarrier->fromArray($carrierByChannel->toExcelArray(), null, 'M21');

        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save('NetworkScale.xlsx');
        return 'NetworkScale.xlsx';

    }//end export()


}//end class
