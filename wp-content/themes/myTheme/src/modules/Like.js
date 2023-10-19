import $ from 'jquery';

class Like {
    constructor() {
        this.events();
    }

    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this));
    }

    ourClickDispatcher(e) {
        var currentLikeBox = $(e.target).closest(".like-box")
        if (currentLikeBox.attr("data-exists") == 'yes') {
            this.deleteLike(currentLikeBox)
        } else {
            this.createLike(currentLikeBox)
        }
    }

    createLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', data.nonce)
            },
            url: data.root_url + "/wp-json/test_rest/v1/like",
            type: "POST",
            data: {
                'id': currentLikeBox.attr('data-id')
            },
            success: (response) => {
                currentLikeBox.attr('data-exists', 'yes')
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10)
                likeCount++
                currentLikeBox.find(".like-count").html(likeCount);
                currentLikeBox.attr("data-like", response)
            },
            error: (response) => {
                console.log("error create");
            }
        })
    }

    deleteLike(currentLikeBox) {
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', data.nonce)
            },
            url: data.root_url + "/wp-json/test_rest/v1/like",
            type: "DELETE",
            data: {
                'id': currentLikeBox.attr('data-like')
            },
            success: (response) => {
                currentLikeBox.attr('data-exists', 'no')
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10)
                likeCount--
                currentLikeBox.find(".like-count").html(likeCount);
                currentLikeBox.attr("data-like", '')
            },
            error: (response) => {
                console.log("error delete")
            }
        })
    }
}

export default Like;