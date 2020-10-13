<?php

namespace App\Sharp;

use App\Eloquent\CrawlStat;
use App\Eloquent\Product\Product;
use App\Eloquent\Product\Vendor;
use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\Dashboard\Layout\DashboardLayoutRow;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;
use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;
use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Dashboard\Widgets\SharpPieGraphWidget;
use Code16\Sharp\Utils\LinkToEntity;
use DB;

class CompanyDashboard extends SharpDashboard
{
    function buildWidgets()
    {
        $this->addWidget(
            SharpPieGraphWidget::make('capacities_pie')
                ->setTitle('Created\Updated last crawling')
        )->addWidget(
            SharpLineGraphWidget::make('capacities')
                ->setTitle('Crawling progress stat')
        )->addWidget(
            SharpPanelWidget::make('updatedComefor')
                ->setInlineTemplate("<h1>{{count}}</h1> Updated Products ComeFor for last week")
//                ->setLink('product')
        )->addWidget(
            SharpPanelWidget::make('createdComefor')
                ->setInlineTemplate("<h1>{{count}}</h1> Created Products ComeFor for last week")
        )->addWidget(
            SharpPanelWidget::make('updatedEMM')
                ->setInlineTemplate("<h1>{{count}}</h1> Updated Products EMM for last week")
        )->addWidget(
            SharpPanelWidget::make('createdEMM')
                ->setInlineTemplate("<h1>{{count}}</h1> Created Products EMM for last week")
        )->addWidget(
            SharpPanelWidget::make('updatedMatrolux')
                ->setInlineTemplate("<h1>{{count}}</h1> Updated Products Matrolux for last week")
        )->addWidget(
            SharpPanelWidget::make('createdMatrolux')
                ->setInlineTemplate("<h1>{{count}}</h1> Created Products Matrolux for last week")
        )->addWidget(
            SharpOrderedListWidget::make('topTravelledSpaceshipModels')
                ->setTitle('Updated products by last 7 days')
                ->buildItemLink(function(LinkToEntity $link, $item) {
                    return $link
                        ->setEntityKey('product')
                        ->addFilter('id', $item['id']);
                })
        );
    }

    function buildWidgetsLayout()
    {
        $this
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'capacities_pie')
                    ->addWidget(6, 'capacities');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'createdComefor')
                    ->addWidget(6, 'updatedComefor');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'createdEMM')
                    ->addWidget(6, 'updatedEMM');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'createdMatrolux')
                    ->addWidget(6, 'updatedMatrolux');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(12, 'topTravelledSpaceshipModels');
            });
    }

    function buildWidgetsData(DashboardQueryParams $params)
    {

        $crawlStats = CrawlStat::query()->where('created_at', '>', $this->getLastWeekTime())->get();

        $createdGraph = [];
        $updatedGraph = [];
        /** @var CrawlStat $crawlStat */
        foreach ($crawlStats as $crawlStat) {
            $createdGraph[(string)$crawlStat->created_at] = $crawlStat->created;
            $updatedGraph[(string)$crawlStat->created_at] = $crawlStat->updated;
        }



        $this->addGraphDataSet(
            'capacities',
            SharpGraphWidgetDataSet::make($createdGraph)
                ->setLabel('Created')
                ->setColor('#3e9651')

        );

        $this->addGraphDataSet(
            'capacities',
            SharpGraphWidgetDataSet::make($updatedGraph)
                ->setLabel('Updated')
                ->setColor('#6b4c9a')
        );

        //pie

        $this->addGraphDataSet(
            'capacities_pie',
            SharpGraphWidgetDataSet::make([
                CrawlStat::query()->orderByDesc('created_at')->pluck('updated')->first()
            ])
                ->setLabel('Updated')
                ->setColor('#3e9651')
        );

        $this->addGraphDataSet(
            'capacities_pie',
            SharpGraphWidgetDataSet::make([
                CrawlStat::query()->orderByDesc('created_at')->pluck('created')->first()
            ])
                ->setLabel('Created')
                ->setColor('#6b4c9a')
        );

        $this->addGraphDataSet(
            'capacities_pie',
            SharpGraphWidgetDataSet::make([
                CrawlStat::query()->orderByDesc('created_at')->pluck('crawled')->first()
            ])
                ->setLabel('Crawled')
                ->setColor('#2d2d2d')
        );

        $this->setPanelData(
            'updatedComefor', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.updated_at', '>', $this->getLastWeekTime())
                ->where('vendor.slug', Vendor::SLUG_COMEFOR)
                ->count()]
        )->setPanelData(
            'createdComefor', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.created_at', '>', $this->getLastWeekTime())
                ->where('vendor.slug', Vendor::SLUG_COMEFOR)
                ->count()]
        );
        $this->setPanelData(
            'createdEMM', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.created_at', '>', $this->getLastWeekTime())
                ->where('vendor.slug', Vendor::SLUG_EMM)
                ->count()]
        )->setPanelData(
            'updatedEMM', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.updated_at', '>', $this->getLastWeekTime())
                ->where('vendor.slug', Vendor::SLUG_EMM)
                ->count()]
        );
        $this->setPanelData(
            'createdMatrolux', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.created_at', '>', $this->getLastWeekTime())
                ->where('vendor.name', Vendor::SLUG_MATROLUX)
                ->count()]
        )->setPanelData(
            'updatedMatrolux', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->leftJoin('vendor', 'category.vendor_id', '=', 'vendor.id')
                ->where('product.updated_at', '>', $this->getLastWeekTime())
                ->where('vendor.name', Vendor::SLUG_MATROLUX)
                ->count()]
        );

        $this->setOrderedListData(
            'topTravelledSpaceshipModels',
            Product::query()
                ->take(50)
                ->get()
                ->map(function(Product $item) {
                    return [
                        'id' => $item->id,
                        'label' => $item->name,
//                        'count' => $item->id,
                        'updated_at' => $item->updated_at,
                    ];
                })
                ->where('updated_at', '>', $this->getLastWeekTime())
                ->sortByDesc('updated_at')
                ->values()
                ->all()
        );
    }

    /**
     * @return string
     */
    private function getLastWeekTime(): string
    {
        return date('Y-m-d', strtotime('-1 week'));
    }
}
