- Login feature
	-When logged in
		-News Feed (use Matrix admin News Updates in widgets)
			* Select flags like a guild or an objective for updates to go straigt to the feed
				*example = guild claims, objective flipped/upgraded, tactics slotted in specific objective (smc got cloaking waters!!!!)
		-User Notes
			*Users can add notes to specific objects or data
				*example = SMC, Mag loves this thing -.-
							Tempest Wolves, Transfered to NSP a week before PIP's were released
			*Notes will be Private by default (toggle coming later)
	-User login back-end
		-Tables
			*users(id, username, password, email)
			*user_preferences(user_id, preference_id)
			*preferences(id, name, effect)
				- eg timezone, activity-feed-flags
			*user_permissions(user_id, permission_id)
			*permissions(id, name, description)
				-make sure to add admin, super-user / God-mode, private-notes, public-notes
			*users_notes(id, user_id, model, model_id, note, private bool)
- feedback button on main site
	- only when logged in
	- store entry into user_notes

- history analyser idea: view hand-picked matchups
	- utilize match-detail-analyser, having checkboxes on that page; each checkbox is a match_detail_id
		- pass match_detail_id to any other analyser to view all of those matchups' data

- match_details page
	- add bronze/silver/gold borders to 1st/2nd/3rd based on skirmish scores
	- add new page for matchup predictions (using current matches; retrofit later for back-in-time)