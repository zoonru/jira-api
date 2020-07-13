<?php

namespace Zoon\Jira;

class CreateReport
{
    public function __construct() {

    }

    public function report(string $sprint, string $teamLead): void {
        $checked_in = [];
        //bugs
        //all features
        $jql = "PROJECT = Main AND Sprint IN {$sprint} AND type = 'Bug' AND status != Closed AND 'Team Lead' IN {$teamLead} order by priority";
        $features = array();
        $sprintBugs = \Badoo\Jira\Issue::search($jql);

        foreach ($sprintBugs as $bug) {
            $checked_in[$bug->getKey()] = $bug->getKey();

            $features[] = array(
                'type' => 'Bug',
                'key' =>  'https://zoonru.atlassian.net/browse/' . $bug->getKey(),
                'summary' => $bug->getSummary(),
                'status' => $bug->getStatus()->getName(),
                'priority' => $bug->getPriority()->getName()
            );
        }

        //all features
        $jql = "PROJECT = Main AND Sprint IN {$sprint} AND type = 'Product Feature' AND status not in (Closed, Approval) AND 'Team Lead' IN {$teamLead} order by priority";

        $sprintFeatures = \Badoo\Jira\Issue::search($jql);

        foreach ($sprintFeatures as $feature) {
            $links = $feature->getLinksList();
            $tasks = array();

            foreach($links->getLinksInward() as $ln) {
                if ($ln->getType()->getName() == 'is-subtask-of') {
                    $issue = $ln->getInwardIssue();
                    if (\Zoon\Jira\Sprint::isIssueInSprint($issue->getKey(), $sprint, $teamLead)) {
                        $checked_in[$issue->getKey()] = $issue->getKey();

                        $tasks[] = array(
                            'type' => $issue->getType()->getName(),
                            'key' => 'https://zoonru.atlassian.net/browse/' . $issue->getKey(),
                            'summary' => $issue->getSummary(),
                            'status' => $issue->getStatus()->getName(),
                            'priority' => $issue->getPriority()->getName());
                    } else {
                        print $issue->getKey() . '!!!!    ';
                    }
                }
            }
            $checked_in[$feature->getKey()] = $feature->getKey();

            $features[] = array(
                'type' => "Feature",
                'key' => 'https://zoonru.atlassian.net/browse/' . $feature->getKey(),
                'summary' => $feature->getSummary(),
                'status' => $feature->getStatus()->getName(),
                'priority' => $feature->getPriority()->getName(),
                'tasks' => $tasks);
        }

        $jql = "PROJECT = Main AND Sprint IN {$sprint} AND type != 'Product Feature' AND status not in (Closed, Approval) AND 'Team Lead' IN {$teamLead} order by priority";

        $allSprintIssues = \Badoo\Jira\Issue::search($jql);

        foreach ($allSprintIssues as $issue) {

            if (!isset($checked_in[$issue->getKey()])) {
                $features[] = array(
                    'type' => $issue->getType()->getName(),
                    'key' => 'https://zoonru.atlassian.net/browse/' . $issue->getKey(),
                    'summary' => $issue->getSummary(),
                    'status' => $issue->getStatus()->getName(),
                    'priority' => $issue->getPriority()->getName()
                );
            }
        }

        //output
        foreach ($features as $feature) {
            print "{$feature['type']},{$feature['key']},{$feature['summary']},{$feature['status']},{$feature['priority']}\n";
            if (isset($feature['tasks'])) {
                foreach ($feature['tasks'] as $issue) {
                    print "{$issue['type']},{$issue['key']},{$issue['summary']},{$issue['status']},{$feature['priority']}\n";
                }
            }
        }
    }
}

