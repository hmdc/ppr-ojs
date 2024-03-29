<?php

import('util.PPRObjectFactory');

import('lib.pkp.classes.user.UserDAO');

/**
 * Utility functions for tests
 */
class PPRTestUtil {

    private $testCase;

    public function __construct($testCase) {
        $this->testCase = $testCase;
    }

    public function createSubmissionWithAuthors($primaryAuthorName, $contributorsNames = []) {
        $primaryAuthor = null;
        $contributors = [];

        if ($primaryAuthorName) {
            $primaryAuthor = $this->createAuthor($this->testCase->getRandomId(), $primaryAuthorName, $primaryAuthorName);
            $contributors[] = $primaryAuthor;
        }

        foreach ($contributorsNames as $name) {
            $contributor = $this->createAuthor($this->testCase->getRandomId(), $name, $name);
            $contributors[] = $contributor;
        }

        $contributorsNames = array_map(function($contributor){return $contributor->getFullName();}, $contributors);
        $submission = $this->testCase->createMock(Submission::class);
        $submission->method('getId')->willReturn($this->testCase->getRandomId());
        $submission->method('getPrimaryAuthor')->willReturn($primaryAuthor);
        $submission->method('getAuthors')->willReturn($contributors);
        $submission->method('getAuthorString')->willReturn(implode(", ", $contributorsNames));
        return $submission;
    }

    public function createAuthor($id, $familyName, $givenName = null) {
        $author = $this->testCase->createMock(Author::class);
        $author->method('getId')->willReturn($id);
        $author->method('getLocalizedFamilyName')->willReturn($familyName);
        $author->method('getLocalizedGivenName')->willReturn($givenName);
        $author->method('getFullName')->willReturn($familyName);
        $author->method('getEmail')->willReturn("$familyName@email.com");
        return $author;
    }

    public function createUser($id, $familyName, $givenName = null) {
        $author = $this->testCase->createMock(User::class);
        $author->method('getId')->willReturn($id);
        $author->method('getUsername')->willReturn(strtolower($familyName));
        $author->method('getLocalizedFamilyName')->willReturn($familyName);
        $author->method('getLocalizedGivenName')->willReturn($givenName);
        $author->method('getFullName')->willReturn($familyName);
        $author->method('getEmail')->willReturn("$familyName@email.com");
        return $author;
    }

    public function createSubmission($submissionId = null) {
        $submissionId ??= $this->testCase->getRandomId();
        $submission = $this->testCase->createMock(Submission::class);
        $submission->method('getId')->willReturn($submissionId);
        $submission->method('getContextId')->willReturn($this->testCase->getRandomId());
        return $submission;
    }

    public function createReview($reviewerId = null) {
        $reviewerId = $reviewerId ?? $this->testCase->getRandomId();
        $review = $this->testCase->createMock(ReviewAssignment::class);
        $review->method('getId')->willReturn($this->testCase->getRandomId());
        $review->method('getReviewerId')->willReturn($reviewerId);
        return $review;
    }

    public function createObjectFactory() {
        $objectFactory = $this->testCase->createMock(PPRObjectFactory::class);
        $submissionUtil = $this->testCase->createMock(PPRSubmissionUtil::class);
        $firstNamesService = $this->testCase->createMock(PPRFirstNamesManagementService::class);
        $objectFactory->method('submissionUtil')->willReturn($submissionUtil);
        $objectFactory->method('firstNamesManagementService')->willReturn($firstNamesService);

        return $objectFactory;
    }

}