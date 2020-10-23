<?php

namespace App\Sharp;

use App\Eloquent\CrawlStat;
use App\Eloquent\Product\Product;
use App\Eloquent\Product\Vendor;
use App\Eloquent\ProductToClient;
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
            SharpPanelWidget::make('updatedProducts')
                ->setInlineTemplate("<h1>{{count}}</h1> Updated Products for last week")
                ->setLink('product')
        )->addWidget(
            SharpPanelWidget::make('createdProducts')
                ->setInlineTemplate("<h1>{{count}}</h1> Created Products for last week")
                ->setLink('product')
        )->addWidget(
            SharpPanelWidget::make('syncProducts')
                ->setInlineTemplate("<h1>{{count}}</h1> Sync Products for last week")
        )->addWidget(
            SharpPanelWidget::make('addedToSite')
                ->setInlineTemplate("<h1>{{count}}</h1> Added Products to Sites for last week")
        )

        ->addWidget(
            SharpPanelWidget::make('runCrawling')
                ->setTemplatePath('dashboard/run-crawling.vue')

        )->addWidget(
            SharpPanelWidget::make('runSync')
                ->setTemplatePath('dashboard/run-sync.vue')
        )

        ->addWidget(
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
                $row->addWidget(6, 'createdProducts')
                    ->addWidget(6, 'updatedProducts');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'addedToSite')
                    ->addWidget(6, 'syncProducts');
            })
            ->addRow(function(DashboardLayoutRow $row) {
                $row->addWidget(6, 'runSync')
                    ->addWidget(6, 'runCrawling');
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
            $createdAt = (new \DateTime($crawlStat->created_at))->format('Y-m-d');
            $updatedAt = (new \DateTime($crawlStat->created_at))->format('Y-m-d');
            $createdGraph[(string)$createdAt] = $createdAt;
            $updatedGraph[(string)$updatedAt] = $updatedAt;
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
            'updatedProducts', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->where('product.updated_at', '>', $this->getLastWeekTime())
                ->count()]
        )->setPanelData(
            'createdProducts', ['count' => Product::query()
                ->leftJoin('category', 'product.category_id', '=', 'category.id')
                ->where('product.created_at', '>', $this->getLastWeekTime())
                ->count()]
        );
        $this->setPanelData(
            'addedToSite', ['count' => ProductToClient::query()
                ->where('created_at', '>', $this->getLastWeekTime())
                ->count()]
        )->setPanelData(
            'syncProducts', ['count' => ProductToClient::query()
                ->where('updated_at', '>', $this->getLastWeekTime())
                ->count()]
        );
        $lastCrawling = CrawlStat::query()->orderByDesc('created_at')->pluck('created_at')->first();

        $this->setPanelData(
            'runSync', [
                'lastSync' => (new \DateTime($lastCrawling))->add(new \DateInterval('PT3H'))->format('Y-m-d'),
                'syncRoute' => route('run-sync')
            ]
        );

        $this->setPanelData(
        'runCrawling', [
            'lastCrawling' => (new \DateTime($lastCrawling))->format('Y-m-d'),
            'crawlingRoute' => route('run-crawling')
            ]
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
