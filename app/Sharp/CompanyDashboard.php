<?php

namespace App\Sharp;

use App\Eloquent\CrawlStat;
use App\Eloquent\Product\Product;
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
            SharpPieGraphWidget::make("capacities_pie")
                ->setTitle('Created\Updated last 7 days')
        )->addWidget(
            SharpLineGraphWidget::make("capacities")
                ->setTitle('Crawling progress stat')
        )->addWidget(
            SharpOrderedListWidget::make("topTravelledSpaceshipModels")
                ->setTitle('Updated products by last 7 days')
                ->buildItemLink(function(LinkToEntity $link, $item) {
                    if($item['id'] >= 5) {
                        return null;
                    }
                    return $link
                        ->setEntityKey("spaceship")
                        ->addFilter("type", $item['id']);
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
                CrawlStat::query()->where('created_at', '>', $this->getLastWeekTime())->sum('updated')
            ])
                ->setLabel('Updated')
                ->setColor('#3e9651')
        );

        $this->addGraphDataSet(
            'capacities_pie',
            SharpGraphWidgetDataSet::make([
                CrawlStat::query()->where('created_at', '>', $this->getLastWeekTime())->sum('created')
            ])
                ->setLabel('Created')
                ->setColor('#6b4c9a')
        );

        $this->addGraphDataSet(
            'capacities_pie',
            SharpGraphWidgetDataSet::make([
                CrawlStat::query()->where('created_at', '>', $this->getLastWeekTime())->sum('crawled')
            ])
                ->setLabel('Crawled')
                ->setColor('#2d2d2d')
        );

//        $this->setPanelData(
//            'activeSpaceships', ['count' => $spaceships->where('state', 'active')->first()->count]
//        )->setPanelData(
//            'inactiveSpaceships', ['count' => $spaceships->where('state', 'inactive')->first()->count]
//        );

        $this->setOrderedListData(
            'topTravelledSpaceshipModels',
            Product::query()
                ->take(10)
                ->get()
                ->map(function(Product $item) {
                    return [
                        'id' => $item->id,
                        'label' => $item->name,
                        'count' => $item->id >= 5 ? null : rand(20, 100),
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
