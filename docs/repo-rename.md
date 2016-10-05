## Repo rename docs

Two significant changes were made:

- The repo was renamed from INN/citylimits to INN/umbrella-citylimits
- The repo's base directory moved from `/wp-content/` to `/` in the wordpress install

### Update process

In this repository's folder, at `/wp-content/`:

```sh
git remote set-url origin git@github.com:INN/umbrella-citylimits.git
git fetch origin
git checkout master
git branch -u origin/master
git pull
```

At this point, your repository will be very confused. From the point of view of the wordpress install, your themes will be in `/wp-content/wp-content/themes/` and your plugins will be in `/wp-content/wp-content/plugins/`, but your uploads will still be in `/wp-content/uploads/`. We're going to fix that.

Change back to the `/` root of the WordPress install.


