// navigation Object
var Display = {};

Display.main = function() {
    game.visible.tab = 'main';

    jQuery('.init-area')
        .removeClass('hidden fadeOutLeftBig')
        .addClass('bounceInLeft');

    Sound.play('buttonClick');
};

Display.rules = function() {
    game.visible.tab = 'rules';

    jQuery('.init-area')
        .removeClass('bounceInLeft')
        .addClass('fadeOutLeftBig');

    jQuery('.rules-area')
        .removeClass('hidden fadeOutRightBig')
        .addClass('bounceInRight');

    Sound.play('buttonClick');
};

Display.back2init = function() {
    game.visible.tab = 'main';

    jQuery('.init-area')
        .removeClass('fadeOutLeftBig')
        .addClass('bounceInLeft');

    jQuery('.rules-area')
        .removeClass('hidden bounceInRight')
        .addClass('fadeOutRightBig');

    Sound.play('buttonClick');
};

Display.back2init7options = function() {
    game.visible.tab = 'main';

    jQuery('.init-area')
        .removeClass('fadeOutLeftBig')
        .addClass('bounceInLeft');

    jQuery('.game-options')
        .removeClass('hidden bounceInRight')
        .addClass('fadeOutRightBig');

    Sound.play('buttonClick');
};

Display.gameStart = function() {
    game.visible.tab = 'options';

    jQuery('.init-area')
        .removeClass('bounceInLeft')
        .addClass('fadeOutLeftBig');

    jQuery('.game-options')
        .removeClass('hidden fadeOutRightBig')
        .addClass('bounceInRight');

    Sound.play('buttonClick');
};

Display.gamePlayingArea = function() {
    game.visible.tab = 'gamePlayingArea';

    jQuery('.game-options')
        .removeClass('bounceInLeft')
        .addClass('fadeOutLeftBig');

    Sound.play('buttonClick');

    $('.js-team-name').each(function() {
        game.teams.push({
            name: $(this).val(),
            score: 0,
            time: 0,
            roundsPlayed: 0
        });
    });

    game.maxPoints = parseInt($('#maxPoints').val());
    game.maxRounds = parseInt($('#maxDuration').val());
    game.maxSeconds = parseInt($('#maxDurationSeconds').val());

    $('.js-dict-names:checked').each(function() {
        game.dicts.push({
            name: $(this).data('id'),
        });
    });

    var xhr = $.ajax({
        url: $('body').data('url') + "/users",
        data: {
            'cat_ids': game.dicts
        },
        beforeSend: function() {
            $('.spinner').show();
        },
        success: function(data) {
            if (undefined !== data.Error) {
                swal({
                    title: 'Error',
                    text: data.Error,
                    icon: 'error',
                    className: 'lang',
                    button: langs[cookie.get('lang')]['TEXT_OK'],
                    dangerMode: true
                }).then((result) => {
                    game.fullReset(false);
                });
                return;
            }
            // console.log(data)
            game.words = data;
            //words goes here as one array
            game.deck = game.shuffle();
            jQuery('.game-playing-area')
                .removeClass('hidden fadeOutRightBig')
                .addClass('bounceInRight');
            $('.spinner').hide();

            jQuery('#js-round-text').text(`0/${game.maxRounds}`);
            jQuery('.js-team-name-text').text(`${game.teams[0]['name']}`);

        },
        method: 'post',
        error: function(e) {
            swal('error');
        }
    });

};

Display.teamPlayingArea2back = function() {
    game.visible.tab = 'teamPlayingArea2back';

    jQuery('.game-playing-area')
        .removeClass('hidden fadeOutLeftBig')
        .addClass('bounceInRight');

    jQuery('.team-playing-area')
        .removeClass('bounceInRight')
        .addClass('fadeOutLeftBig');

    Sound.play('buttonClick');
};

Display.teamPlayingArea = function() {
    game.visible.tab = 'teamPlayingArea';

    jQuery('.game-playing-area')
        .removeClass('bounceInRight')
        .addClass('fadeOutLeftBig');

    jQuery('.team-playing-area')
        .removeClass('hidden fadeOutLeftBig')
        .addClass('bounceInRight');

    Sound.play('buttonClick');

    $('#js-word').text(game.generateWord().word);

    // game starts here with the timer
    var Timer = new CountDownTimer(game.maxSeconds);
    Timer.onTick(function(minutes, seconds) {
        seconds = minutes * 60 + seconds;
        $('#js-timer').text(seconds);

        //game func here
        // console.log(game.teams, game.steps, game.teams[game.steps % game.teams.length])

        jQuery('.js-team-round-updater').text(`${game.teams[game.steps % game.teams.length].roundsPlayed}/${game.maxRounds}`);
        jQuery('.js-team-name-updater').text(`${game.teams[game.steps % game.teams.length]['name']}`);
        //set beeper
        if (seconds <= 10 && seconds >= 5) {
            Sound.play('beep');
        } else if (seconds < 5 && seconds > 0) {
            Sound.play('beep');
            setTimeout(function() {
                Sound.play('beep');
            }, 500);
        }

        if (seconds == 0) {
            //go back
            var queueCounter = game.steps % game.teams.length;
            game.teams[queueCounter].roundsPlayed++;
            jQuery('#js-round-text')
                .text(`
						${game.teams[game.teams.length-1>queueCounter?queueCounter+1:0].roundsPlayed}/${game.maxRounds}
					`);
            jQuery('.js-team-name-text')
                .text(`${game.teams[game.teams.length-1>queueCounter?queueCounter+1:0]['name']}`);

            Display.teamPlayingArea2back();

            // console.log(queueCounter, 'ended');

            // team id counter
            game.steps++;
            //check winner here
            //check if last players round played has finished
            if (game.teams[game.teams.length - 1]['roundsPlayed'] == game.teams[0]['roundsPlayed']) {
                var teamWinner = game.checkWinner();
                if (false !== teamWinner) {
                    // yay winner is here
                    game.ended = true;
                    game.winnerID = teamWinner.ID
                    game.winner(teamWinner.data.name);

                }
            }
            //whether rounds ended max ponts excedded or sometin elese
        }
    }).start();

};

// shuffle words
game.shuffle = function() {
    var wrds = new Array();
    game.words
        .forEach(function(dict) {
            dict.forEach(function(word) {
                wrds.push(word);
            });
        });

    return wrds;
};

game.generateWord = function() {
    if (game.deck.length == 0) {
        swal('Error', 'Out of words', 'error');
        return 'no word';
    }
    var rnd = getRndInteger(0, game.deck.length - 1);
    var pickOne = game.deck[rnd];
    game.deck.splice(rnd, 1);

    return pickOne;
};

game.checkWinner = function() {
    var max = -1,
        maxId = -1;

    game.teams.forEach(function(team, index) {
        if (max < team.score) {
            max = team.score;
            maxId = index;
        }
    });
    // check winner
    // if last element round equals maxROunds
    if (game.maxRounds == game.teams[game.teams.length - 1]['roundsPlayed']) {
        // max rounds here
        return {
            data: game.teams[maxId],
            ID: maxId
        };
    } else if (game.teams[maxId].score >= game.maxPoints) {
        // we have a winner
        return {
            data: game.teams[maxId],
            ID: maxId
        };
    } else {
        return false;
    }
};


jQuery(document).ready(function() {
    $('.spinner').hide();
    Display.main();
    // Display.gameStart();
    // Display.gamePlayingArea();
    // Display.teamPlayingArea();
});

jQuery('body')
    .on('swipeleft', function() {
        switch (true) {
            case game.visible.tab == 'main':
                Display['gameStart']();
                break;
            case game.visible.tab == 'options':
                Display['gamePlayingArea']();
                break;
        }
    })
    .on('swiperight', function() {
        switch (true) {
            case game.visible.tab == 'options':
                Display['back2init7options']();
                break;
            case game.visible.tab == 'rules':
                Display['back2init']();
                break;
        }
    });

jQuery('#rules-btn').click(Display.rules);
jQuery('#from-rules-go-back').click(Display.back2init);
jQuery('#gameStart').click(Display.gameStart);
jQuery('#js-start2game-area').click(Display.gamePlayingArea);
jQuery('#js-back-options-main').click(Display.back2init7options);
jQuery('#startRound').click(Display.teamPlayingArea);

jQuery('.js-correct').click(function() {
    Sound.play('correctClick');
    $('.team-playing-area').addClass('bg-success-light');
    setTimeout(function() {
        $('.team-playing-area').removeClass('bg-success-light');
    }, 500);
    $('#js-word').text(game.generateWord().word);
    game.teams[game.steps % game.teams.length].score++;
    $(this).attr('disabled', 'true');
    var sthis = this;
    setTimeout(function() {
        $(sthis).removeAttr('disabled');
    }, 700);
});
jQuery('.js-wrong').click(function() {
    Sound.play('wrongClick');
    $('.team-playing-area').addClass('bg-danger-light');
    setTimeout(function() {
        $('.team-playing-area').removeClass('bg-danger-light');
    }, 500);
    $('#js-word').text(game.generateWord().word);
    game.teams[game.steps % game.teams.length].score--;
    $(this).attr('disabled', 'true');
    var sthis = this;
    setTimeout(function() {
        $(sthis).removeAttr('disabled');
    }, 700);
});

// end of navigation of RULES

jQuery('.js-click-btn, .scoreBoardModal, .langChangeModal, #lang_ka, #lang_en, #lang_me')
    .on('click', function() {
        Sound.play('buttonClick');
    });

// max points button clicks
jQuery('.js-btn-max-pts').click(function() {
    Sound.play('buttonClick');

    $('#maxPoints').val($(this).data('value'));
    $('.js-max-points-counter').text($(this).data('value'));
});
jQuery('.js-btn-duration').click(function() {
    Sound.play('buttonClick');

    $('#maxDuration').val($(this).data('value'));
    $('.js-max-duration-counter').text($(this).data('value'));
});
jQuery('.js-btn-duration-seconds').click(function() {
    Sound.play('buttonClick');

    $('#maxDurationSeconds').val($(this).data('value'));
    $('.js-max-duration-seconds-counter').text($(this).data('value'));
});
jQuery('#maxPoints').on('input', function() {
    if ($(this).val() > game.default.maxPoints) {
        $(this).val(function() {
            return this.value = this.value.slice(0, -1);
        });
    }
    $('.js-max-points-counter').text($(this).val());
});
jQuery('#maxDurationSeconds').on('input', function() {
    if ($(this).val() > game.default.maxSeconds) {
        $(this).val(function() {
            return this.value = this.value.slice(0, -1);
        });
    }
    $('.js-max-duration-seconds-counter').text($(this).val());
});
jQuery('#maxDuration').on('input', function() {
    if ($(this).val() > game.default.maxDuration) {
        $(this).val(function() {
            return this.value = this.value.slice(0, -1);
        });
    }
    $('.js-max-duration-counter').text($(this).val());
});
jQuery('body').on('input', '.js-team-name', function() {
    if ($(this).val().length > game.default.maxTeamNameLen) {
        $(this).val(function() {
            return this.value = this.value.slice(0, -1);
        });
    }
});


jQuery('#js-max-points').text(game.default.maxPoints);
jQuery('#js-max-rounds').text(game.default.maxDuration);
jQuery('#js-max-seconds').text(game.default.maxSeconds);

//generate select options
Array(9).join('1').split('').forEach(function(el, index) {
    jQuery('#teamCount').append(`
			<option value="${index+3}">${index+3}</option>
		`);
});

// on select change generate inputs for tem names
jQuery('#teamCount').on('change', function() {
    $('.js-team-name').remove();
    $('.js-teams-counter').text($(this).val());

    new Array().fill(0, $(this).val())
        .forEach(function(element, index) {
            $('.js-team-names-div').append(`
				<input tabindex="${index+1}" type="text" value="Team-${index+1}" class="font-weight-bold js-team-name lang TEXT_TEAM${index+1} w-100 mt-3 form-control rounded-0 border-danger text-danger">
			`);
        });
    translate();
});


// small functionality
jQuery('body').on('focus click', '.js-team-name', function() {
    this.select();
});

// reset game click
jQuery('.js-reset-game').click(x => {
    Sound.play('buttonClick');
    game.fullReset();
});

game.fullReset = function(confirm = true) {
    if (confirm) {
        swal({
            title: langs[cookie.get('lang')]['TEXT_ARE_U_SURE'],
            text: langs[cookie.get('lang')]['TEXT_CANT_REVERT'],
            icon: 'warning',
            className: 'lang',
            buttons: [
                langs[cookie.get('lang')]['TEXT_NO'],
                langs[cookie.get('lang')]['TEXT_YES']
            ],
            dangerMode: true
        }).then((result) => {
            if (result) {
                game.refreshing = true;
                $('.spinner').show();
                $('.game-playing-area').hide();
                window.location.reload();
            }
        });
    } else {
        Sound.play('buttonClick');

        game.refreshing = true;
        $('.spinner').show();
        $('.game-playing-area').hide();
        window.location.reload();
    }
};


// generate categories
(function generateCategories() {
    window.generateCategories = generateCategories;
    jQuery.ajax({
        url: $('body').data('url') + "/users/categories",
        beforeSend: function() {
            // console.log('categories is being loading');
        },
        success: function(data) {
            // console.log(data)
            $('.js-dict-names-div').empty();
            data.forEach(function(el, ix) {
                $('.js-dict-names-div').append(`
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<div class="input-group-text bg-danger border-0 rounded-0">
									<input type="checkbox" id="dict${el.cat_id}" class="js-dict-names" checked="true" data-name="${el.cat_name}" data-id="${el.cat_id}">
								</div>
							</div>
							<button class="btn rounded-0 btn-danger form-control">
								<label for="dict${el.cat_id}">${el.cat_name} <span class="badge badge-danger">${el['length']}</span></label>
							</button>
						</div>
					`);
            });
        },
        method: 'post',
        error: function() {
            swal('Error cat loading');
        }
    });

})();


// update functions
game.update = {
    scoreBoard: function() {
        $('#scoreBoard').empty();
        $('#scoreBoard').append(`
			<div class="row w-100 mb-2">
				<div class="lang col col-6 font-weight-bold">${langs[cookie.get('lang')]['TEXT_TEAMS']}</div>
				<div class="lang col col-3 font-weight-bold">${langs[cookie.get('lang')]['TEXT_PLAYED']}</div>
				<div class="lang col col-3 font-weight-bold">${langs[cookie.get('lang')]['TEXT_POINTS1']}</div>
			</div>			
		`);

        game.teams.forEach(function(team, i) {
            var className = game.ended && game.winnerID == i ? 'bg-warning' : '';

            $('#scoreBoard').append(`
				<div class="row w-100 ${className}">
					<div class="lang col col-6 overme">${team.name}</div>
					<div class="lang col col-3">${team.roundsPlayed}/${game.maxRounds}</div>
					<div class="lang col col-3">${team.score}/${game.maxPoints}</div>
				</div>			
			`);
        });
    }
};

// on modal show update scoreBoard
$('#scoreBoardModal').on('show.bs.modal', game.update.scoreBoard);


game.winner = function(teamName) {
    game.sound.win.play();
    swal({
        title: langs[cookie.get('lang')]['TEXT_WINNER'],
        text: langs[cookie.get('lang')]['TEXT_WINNER_DESCRIPTION'] + ` ${teamName}`,
        icon: 'warning',
        className: 'lang',
        button: langs[cookie.get('lang')]['TEXT_OK'],
        dangerMode: true
    }).then((result) => {
        $('#scoreBoardModal').modal('show');
        game.reset();
        // update text 
        //if winner start again and change init text
        jQuery('#js-round-text').text(`${game.teams[game.steps % game.teams.length].roundsPlayed}/${game.maxRounds}`);
        jQuery('.js-team-name-text').text(`${game.teams[game.steps % game.teams.length]['name']}`);
    });
};


// reset game data
game.reset = function() {
    game.ended = false;
    game.steps = 0;
    game.teams.forEach(function(team, index) {
        team.score = 0;
        team.roundsPlayed = 0;
    });
}