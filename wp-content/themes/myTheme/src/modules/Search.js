import $ from 'jquery';

class Search {
    constructor() {
        this.addHTML();
        this.resultDiv = $("#search-overlay__results")
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.isOverlayOpen = false;
        this.typingTimer = null;
        this.isSpinnerVisible = false;
        this.previoursValue = null;
        this.events();
    }

    events() {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this))
        this.searchField.on("keyup", this.typingLogic.bind(this))
    }

    typingLogic() {
        if (this.searchField.val() != this.previoursValue) {
            clearTimeout(this.typingTimer);

            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {
                    this.resultDiv.html("<div class='spinner-loader'></div>")
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750)
            } else {
                this.resultDiv.html("")
                this.isSpinnerVisible = false;
            }
            this.previoursValue = this.searchField.val();
        }
    }

    getResults() {
        $.getJSON(data.root_url + '/wp-json/test_rest/v1/search?term=' + this.searchField.val(), (result) => {
            this.resultDiv.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                            ${result.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information</p>'}
                            ${result.generalInfo.map(item => `<li><a href="${item.permalink}">${item.title}</a> ${item.postType == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
                            ${result.generalInfo.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${result.programs.length ? '<ul class="link-list min-list">' : '<p>No programs information</p>'}
                        ${result.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        ${result.programs.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${result.professors.length ? '<ul class="professor-cards">' : '<p>No professors information</p>'}
                        ${result.professors.map(item => `
                            <li class="professor-card__list-item">
                                <a class="professor-card" href="${item.permalink}">
                                    <img class="professor-card__image" src="${item.image}">
                                    <span class="professor-card__name">${item.title}</span>
                                </a>
                            </li>
                        `).join('')}
                        ${result.professors.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campuses</h2>
                        ${result.campuses.length ? '<ul class="link-list min-list">' : '<p>No campuses information</p>'}
                        ${result.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        ${result.campuses.length ? '</ul>' : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Events</h2>
                        ${result.events.length ? `` : '<p>No events information</p>'}
                        ${result.events.map(item => `
                        <div class="event-summary">
                             <a class="event-summary__date event-summary__date--beige t-center"
                               href="${item.title}">
                                <span class="event-summary__month">${item.date}</span>
                                <span class="event-summary__day">${item.month}</span>
                            </a>
                            <div class="event-summary__content">
                                <h5 class="event-summary__title headline headline--tiny">
                                    <a href="${item.permalink}">${item.description}</a>
                                    <a href="${item.permalink}" class="nu gray">Read more</a>
                                </h5>
                            </div>
                        </div>
                        `).join('')}
                        ${result.events.length ? '' : ''}                    
                    </div>
                </div>
            `)
            this.isSpinnerVisible = false
        });
    }

    keyPressDispatcher(e) {
        if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus")) {
            this.openOverlay();
        }

        if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('')
        setTimeout(function () {
            this.searchField.focus();
        }.bind(this), 301)
        this.isOverlayOpen = true
        return false;
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false
    }

    addHTML() {
        $("body").append(`
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                    <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                    <input type="text" id="search-term" class="search-term" placeholder="What are you looking for?"
                           autocomplete="off">
                    <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
                <div class="container">
                    <div id="search-overlay__results"></div>
                </div>
           </div>
        </div>
        `)
    }
}

export default Search