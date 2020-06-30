<?php

namespace Zoon\Jira;

class Sprint
{
    private static $sprint = array();

    public static function isIssueInSprint($issueKey, $Jira, $sprint, $team_lead): bool {
        if (empty(self::$sprint)) {
            self::fillSprint($Jira, $sprint, $team_lead);
        }

        return in_array($issueKey, self::$sprint);
    }

    private static function fillSprint($Jira, $sprint, $team_lead): void {
        $jql = "PROJECT = Main AND Sprint IN {$sprint} AND 'Team Lead' IN {$team_lead}";
        $sprint = \Badoo\Jira\Issue::search($jql);

        foreach ($sprint as $issue) {
            self::$sprint[] = $issue->getKey();
        }
    }
}
