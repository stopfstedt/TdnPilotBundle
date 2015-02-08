Post Notes
==========
After generation, if you used the pilot, manager, or handler generators please note that
some modifications to your `<BundleName>Extension.php` file are required.

1. Make sure to instantiate any loaders required (yml, xml, ini, etc).
2. Make sure to load the files generated under `Resources/config/*.<format>`

Optimize Queries
----------------
Adding a repository to your entity manager to optimize join queries (or encapsulating
non-standard query) is always a good idea.

Identify any relationships where you'll be needing to join a column on multiple results.

Instead of fetching all and accessing the relationship, use a repository to create a new method
with the appropriate DQL.

- Create a `Repository` directory under your `Entity` namespace `$ mkdir Entity\Repository`.
- Add your repository class `$ touch Entity/Repository/FooRepository.php` and appropriate contents.
- Modify the `managers.yml` and `FooManager.php` files to reflect the new dependency.
- ????
- Profit.
