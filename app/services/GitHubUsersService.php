<?php

namespace CD\services;

/**
 * GitHub Users Service - A service class for retrieving GitHub user data.
 *
 * @author Sean Wallis <sean.wallis2@networkrail.co.uk>
 */
class GitHubUsersService extends HttpClient
{
    /**
     * Retrieve a GitHub user's details and details of their followers.
     *
     * @param string $userName The username of the GitHub user.
     *
     * @return array An array of the user's details.
     */
    public function retrieveData($userName)
    {
        $userDetails = $this->getUserDetails($userName);
        if (isset($userDetails['followers_url'])) {
            $followersData = $this->getFollowers($userDetails['followers_url']);
            return array_merge(
                $userDetails,
                ['followers_data' => $followersData]
            );
        }

        return $userDetails;
    }

    /**
     * Get the GitHub user's basic details
     *
     * @param string $userName The username of the GitHub user.
     *
     * @return array An array of the user's details.
     */
    public function getUserDetails($userName)
    {
        return $this->tryGet('/users/'.$userName);
    }

    /**
     * Get array of a GitHub user's followers
     *
     * @param string $followersUrl The followers url for a GitHub user.
     *
     * @return array An array of a GitHub user's followers.
     */
    public function getFollowers($followersUrl)
    {
        return $this->tryGet($followersUrl);
    }
}
