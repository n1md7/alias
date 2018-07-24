var game = {
	default: {
		maxPoints: 200,
		maxSeconds: 180,
		maxDuration: 100,
		maxTeamNameLen: 20
	},
	visible: {
		tab: 'main'
	},
	teams: [],
	dicts: [],
	maxPoints: 0,
	maxRounds: 0,
	steps: 0,
	currentTimer: 60,
	ended: false,
	refreshing: false,
	maxSeconds: 0,
	sound: {
		buttonClick: $('#button_click')[0],
		correctClick: $('#correct_click')[0],
		wrongClick: $('#wrong_click')[0],
		beep: $('#beep')[0],
		win: $('#win_sound')[0]
	}
};