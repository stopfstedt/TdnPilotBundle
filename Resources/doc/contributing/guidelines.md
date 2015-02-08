
The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119].

Contributors should keep in mind the following rules when creating pull requests:

  * You MUST rebase/pull against the `develop` branch (recommended frequently) and squash your commit when submitting. 
    See the [apache git usage] document which explains the why and this [tutorial] which explains the how. 
    Extra information about rebasing/reflog can be located in the [rebase documentation]
    and this [reflog tutorial] respectively

  * You MUST follow [PSR-1] and [PSR-2]

  * You SHOULD [run the local checks]

  * You MUST write/update unit tests accordingly

  * You MUST write a description which gives context to the PR

  * You SHOULD write/update documentation accordingly

  * The following checks will be automatically performed on PRs:
     - Code Style (PSR-1, PSR-2)
     - Scrutinizer checks
     - PhpUnit tests

Notes:

- If any of those fail the PR will not be merged until it is updated accordingly.
- If you're simply adding your application to the README.md file, the commit will build accordingly.

[run the local checks]: test-checks.md
[apache git usage]: https://cwiki.apache.org/confluence/display/FLEX/Good+vs+Bad+Git+usage
[tutorial]: http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html
[reflog tutorial]: https://www.atlassian.com/git/tutorials/rewriting-history/git-reflog
[rebase documentation]: http://git-scm.com/book/en/v2/Git-Branching-Rebasing
[RFC 2119]: http://www.ietf.org/rfc/rfc2119.txt
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
