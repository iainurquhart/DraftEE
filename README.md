# DraftEE

Module/Fieldtype combo for creating/publishing drafts of Channel Entries.

Just a module I'm experimenting with, very much proof of concept stuff that's nowhere near production use.

### Overview

The fieldtype outputs a 'create draft' button on the publish page. When you hit 'creat draft' - the fieldtype does an ajax call to the module to essentially duplicate the entry and give it a status of Draft. The publisher can then switch to the Draft entry, and do edits there.

The entry can then be edited/saved safely away from the live entry (it's now another entry) but come time to publish/merge, the publisher can easily overwrite the originating entry with the updated content.

All's well in theory, will be interesting when user permissions are thrown in the mix, and custom fieldtypes that store their data outside of exp_channel_data...

Will cross that bridge if I ever get there...