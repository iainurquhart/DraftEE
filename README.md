# DraftEE

### Overview

Module/Fieldtype combo for creating/publishing drafts of Channel Entries.

Just a module I'm experimenting with, very much proof of concept stuff that's nowhere near production use.

Basic overview is that the fieldtype outputs a 'create draft' button on the publish page. When you hit 'creating draft', the fieldtype does an ajax call to the module to essentially duplicate the entry and give it a status of Draft. The publisher then switches to the Draft entry, and does edits there.

The entry can then be edited/saved away from the live entry (it's now another entry) but come time to publish/merge it'll will overwrite the originating entry with the updated content.

All's well in theory, will be interesting when user permissions are thrown in the mix, and custom fieldtypes that store their data outside of exp_channel_data...

Will cross that bridge if I ever get there...