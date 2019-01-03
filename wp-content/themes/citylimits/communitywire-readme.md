# CommunityWire

CommunityWire is a feature on the City Limits website to function as a sort of local bulletin board. It consists of two types of content: events and announcements.

## Landing Page

User facing: https://citylimits.org/communitywire-home/
Edit link: https://citylimits.org/wp-admin/post.php?post=2988252&action=edit

The landing page uses a special template, `page-communitywire.php`. The logo at the top is edited in the page content itself at that edit link. Similarly, that's where the submission forms live as well. We use the Accordion Shortcodes plugin to slot those forms under dropdowns.

The lists of events and announcements are powered under the "CommunityWire Listings" widget area on [the widgets page](https://citylimits.org/wp-admin/widgets.php). This should be filled with two widgets:

 - Events List (which is being filted by the CommunityWire code on the back end)
 - CommunityWire Announcements

## Events

CommunityWire events listings are powered by the Events Calendar plugin. Specifically, they are categorized under their own subcategory, which in turn has further subcategories for Exhibit, Meeting, etc. Rather than use Gravity Forms' webhooks to create these events, due to bugginess, the code to connect the form and the event listing lives in `/inc/communitywire.php`.

Submission form: [Forms > CommunityWire Events](https://citylimits.org/wp-admin/admin.php?page=gf_edit_forms&id=44)
Submitted events: [Events > Event Categories > CommunityWire Events](https://citylimits.org/wp-admin/edit.php?tribe_events_cat=communitywire-events&post_type=tribe_events)

## Announcements

Press release-type announcements are what WordPress refers to as a custom post type, which is created in `/inc/communitywire.php`. This should work exactly the same as a regular post does, though.

Submission form: [Forms > CommunityWire Announcements](https://citylimits.org/wp-admin/admin.php?page=gf_edit_forms&id=43)
Submitted announcements: [CommunityWire Announcements](https://citylimits.org/wp-admin/edit.php?post_type=communitywire)