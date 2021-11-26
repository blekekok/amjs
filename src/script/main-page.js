var currentActiveCategory = null;
var currentActiveUserText = '';

var currentURL = new URLSearchParams(window.location.search);

if (currentURL.has('thread')) {
    let threadId = currentURL.get('thread')
    
    setActiveThread(threadId, true);
} else {
    setGroups();
}

function setGroups(active=-1, categoryid=-1) {
    let groups = getGroups();

    if (groups && groups.length) {
        let groupList = $('#group-list');
        groupList.empty();

        groups.forEach(group => {
            $('<li/>', {
                'class': group.id == active || active == -1 && group.id == groups[0].id ? 'active': '',
                'html': `<span>${group.displayname}</span>`
            }).on('click', function () {
                $('#group-list li').removeClass('active');
                $(this).addClass('active');
                setCategories(group.id);
            }).appendTo(groupList);
        });
        
        if (active == -1) {
            setCategories(groups[0].id);
        } else {
            setCategories(active, categoryid);
        }
    }
}

function setCategories(groupid=1, active=-1) {
    let categories = getCategories(groupid);

    let categoryList = $('#category-list');
    categoryList.empty();
    $('#thread-list').empty();

    currentActiveCategory = null;

    if (categories && categories.length) {
        categories.forEach(category => {
            $('<li/>', {
                'class': category.id == active || (active == -1 && category.id == categories[0].id) ? 'active' : '',
                'html': `<span>${category.displayname}</span>`
            }).on('click', function () {
                $('#category-list li').removeClass('active');
                $(this).addClass('active');
                setThreadTitles(category.id);
            }).appendTo(categoryList);
        });

        if (active == -1) {
            setThreadTitles(categories[0].id);
        } else {
            setThreadTitles(active);
        }
    }
}

function setThreadTitles(categoryid=2) {
    let threads = getThreadTitles(categoryid);
    let threadList = $('#thread-list');
    threadList.empty();

    currentActiveCategory = categoryid;

    if (threads) {

        threads.forEach(thread => {
            let listItem = $('<li/>');

            $('<span/>', {'class': 'badge', 'html' : thread.hot ? '[HOT]' : ''}).appendTo(listItem);
            $('<span/>', {'class': 'title', 'html': thread.title}).on('click', () => {
                setActiveThread(thread.id);
            }).appendTo(listItem);

            let metaDiv = $('<div/>', {'class': 'meta'});

            let subMetaDiv = $('<div/>', {'class': 'sub-meta'});
            $('<span/>', {'class': 'author', 'html': `by ${thread.author}`}).appendTo(subMetaDiv);
            $('<div/>', {'html': `
                <img src="src/res/eye.svg" alt="">
                <span>${convertNumberToShortened(thread.views)}</span>
            `}).appendTo(subMetaDiv);
            subMetaDiv.appendTo(metaDiv);

            // Total Post Counter
            $('<div/>', {'class': 'posts', 'html': `
                <img src="src/res/comment.svg" alt="">
                <span>${convertNumberToShortened(thread.posts)}</span>
            `}).appendTo(metaDiv);

            // Time
            $('<span/>', {'class': 'time', 'html': formatTimeAsText(thread.age)}).appendTo(metaDiv);
            
            metaDiv.appendTo(listItem);
            listItem.appendTo(threadList);
        });
    }
}

function setActiveThread(threadid, fromLink=false) {
    let result = getThreadContent(threadid);

    if (result) {
        let threadContent = result.content;

        if (fromLink) {
            setGroups(threadContent.groupId, threadContent.categoryId);
        }

        $('#bottom-seperator h1').html(`Thread in : ${threadContent.categoryName}`);
        $('#bottom-seperator h2').hide();

        $('#content-header').empty();
        $('#content-header').append(
            `
                <div id="thread-header">
                    <h1>${threadContent.title}</h1>
                    <span>Posted on ${threadContent.threadDate} by ${threadContent.author}</span>
                    <div>
                        <img src="src/res/history.svg" alt="">
                        <span>${formatTimeAsText(threadContent.threadLastActivity)}.</span>
                    </div>
                </div>
                <div class="seperator">
                    <div class="line"></div>
                </div>
            `
        );

        $('#thread-content').empty();

        let threadPost = buildThreadPost('Main Post', threadContent, true, result.content.locked);

        $('#thread-content').append(threadPost);

        $('html,body').animate({scrollTop: threadPost.offset().top}, 'fast');

        let postsContent = result.posts;
        if (postsContent) {
            
            postsContent.forEach(post => {
                $('#thread-content').append(buildThreadPost(post.targetUsername ? `Reply to ${post.targetUsername}` : 'Reply to Main Post', post, false, result.content.locked))
            });

        }
    }
}

function buildThreadPost(title, content, isThread=false, isLocked=false) {

    let postWrapper = $('<div/>', {'class': 'post-wrapper'});
    
    // Post Header
    $('<div/>', {'class': 'post-header', 'html': `
        <span>${title}</span>
        <div>
            <img src="src/res/clock.svg" alt="">
            <span>${formatTimeAsText(content.postDate, false)}</span>
        </div>
    `}).appendTo(postWrapper);
    
    // Post Content
    let postContent = $('<div/>', {'class': 'post-content'});
    
    // User Info
    let userInfo = $('<div/>', {'class': 'user-info'});
    let userProfile = $('<div/>', {'class': 'user-profile'});
    
    // User profile contents
    $('<a/>', {'class': 'avatar', 'html': `<img src="${content.avatarURL ? content.avatarURL : 'src/res/default-user-icon.jpg'}" alt="">`, 'href': `/profile.php?user=${content.author}`}).appendTo(userProfile);
    $('<a/>', {'class': 'username', 'html': content.author, 'href': `/profile.php?user=${content.author}`}).appendTo(userProfile);
    $('<span/>', {'class': 'status', 'html': content.active ? 'Online' : 'Offline'}).appendTo(userProfile);
    userProfile.appendTo(userInfo);
    
    let roleName = 'User';
    switch (content.role) {
        case 'user':
            roleName = 'User';
        break;
        
        case 'mod':
            roleName = 'Moderator';
            break;
            
            case 'siteadmin':
                roleName = 'Site Admin';
        break;
    }
    
    let modStatusText = 'Active';
    switch (content.modStatus) {
        case 1:
            modStatusText = 'Silenced';
            break;
            
        case 2:
            modStatusText = 'Banned';
            break;
    }

    // User Data
    $('<div/>', {'class': 'user-data', 'html': `
        <div><img src="src/res/user-dark.svg" alt=""><span>${roleName}</span></div>
        <div><img src="src/res/pencil.svg" alt=""><span>${content.totalUserPosts} posts</span></div>
        <div><img src="src/res/login.svg" alt=""><span>${formatTimeAsText(content.lastLogin, false)}</span></div>
        <div><img src="src/res/info.svg" alt=""><span>${modStatusText}</span></div>
    `}).appendTo(userInfo);
    userInfo.appendTo(postContent);

    // Post Content
    $('<div/>', {'class': 'post-body', 'html': `<div class="content">${content.postBody}</div>`}).appendTo(postContent);
    postContent.appendTo(postWrapper);

    // Post Footer
    let totalLikes = content.totalPostLikes;
    let postFooter = $('<div/>', {'class': 'post-footer'});

    let postLikes = $('<div/>', {'class': 'favorite', 'html': `
        <img src="src/res/heart.svg" alt="">
        <span>${totalLikes} user${totalLikes > 1 ? 's' : ''} favorited this post</span>
    `}).appendTo(postFooter);

    let postButtons = $('<div/>', {'class': 'post-buttons'});

    //$('<button/>', {'html': '<img src="src/res/heart.svg" alt="">'}).on('click', () => {console.log('nothing for now')}).appendTo(postButtons);

    // Like Button
    if (!content.isAuthor && content.modStatus <= 0) {   
        let isLiked = content.isLiked;
        $('<button/>', {'html': `<img src="src/res/${isLiked ? 'heart-filled.svg' : 'heart.svg'}" alt="">`}).on('click', function () {
            if (updatePostLike(content.id, !isLiked, isThread)) {
                
                if (isLiked) {
                    isLiked = false;
                    totalLikes--;
                } else {
                    isLiked = true;
                    totalLikes++;
                }
                
                $(this).html(`<img src="src/res/${isLiked ? 'heart-filled.svg' : 'heart.svg'}" alt="">`);
                
                $(postLikes).html(`
                    <img src="src/res/heart.svg" alt="">
                    <span>${totalLikes} user${totalLikes > 1 ? 's' : ''} favorited this post</span>
                `);
            }
        }).appendTo(postButtons);
    }

    // Reply Button
    if (content.modStatus <= 0 && !isLocked) {
        $('<button/>', {'html': '<img src="src/res/reply.svg" alt="">'}).on('click', () => {
    
            $('#reply-post').remove();
    
            let replyPost = buildCreatePostBuilder(isThread ? 'Creating Reply to Main Post' : `Creating Reply to ${content.author}`, 1, content.threadId);
            $('#thread-content').append(replyPost);
    
            $('html,body').animate({scrollTop: replyPost.offset().top}, 'fast');
    
        }).appendTo(postButtons);
    }

    
    if (content.isAuthor && content.isEditable) {

        // Edit Button
        $('<button/>', {'html': '<img src="src/res/pencil.svg" alt="">'}).on('click', function () {

            let editPost = buildCreatePostBuilder('Editing Post', 2, content.id, postWrapper, $(this), isThread, content.threadId, content.postBody);
            $(postWrapper).replaceWith(editPost);

        }).appendTo(postButtons);

        // Delete Button
        $('<button/>', {'html': '<img src="src/res/trash-bin.svg" alt="">'}).on('click', () => {
            if (postDelete(content.id, isThread)) {
                if (isThread) {
                    window.location.replace('/');
                } else {
                    window.location.replace(`/index.php?thread=${content.threadId}`);
                }
            }
        }).appendTo(postButtons);
    }

    postButtons.appendTo(postFooter);
        
    postFooter.appendTo(postWrapper);
    
    return postWrapper;
    
}

// On create thread click
$('#create-thread-button').on('click', () => {

    let activeCategory = $('#category-list .active');

    if (activeCategory.length <= 0 && !currentActiveCategory) {
        showError('Error, no category!');
        return;
    }

    $('#bottom-seperator h1').html(`Creating a Thread`);
    $('#bottom-seperator h2').hide();
    $('#content-header').empty();
    $('#thread-content').empty();

    let threadCreatePostBuilder = buildCreatePostBuilder(`Creating Thread in ${$('#category-list .active span').html()}`, 0, currentActiveCategory);

    $('#thread-content').append(threadCreatePostBuilder);

    history.replaceState('', '', '/');

    $('html,body').animate({scrollTop: threadCreatePostBuilder.offset().top}, 'fast');

});

function buildCreatePostBuilder(title, type=0, id=0, param1=null, param2=null, param3=null, param4=null, param5=null) {

    /**
     * 0 -> Create
     * 1 -> Reply
     * 2 -> Edit
     */

     let content = getSelfUserData();

     if (!content) {
         showError('An unknown error occurred!');
         return;
     }

    let postWrapper = $('<div/>', {'class': 'post-wrapper'});
    
    if (type == 1) postWrapper.prop('id', 'reply-post');

    // Post Header
    $('<div/>', {'class': 'post-header create', 'html': `<span>${title}</span>`}).appendTo(postWrapper);
    
    // Post Content
    let postContent = $('<div/>', {'class': 'post-content'});
    
    // User Info
    let userInfo = $('<div/>', {'class': 'user-info'});
    let userProfile = $('<div/>', {'class': 'user-profile'});
    
    // User profile contents
    $('<a/>', {'class': 'avatar', 'html': `<img src="${content.avatarURL ? content.avatarURL : 'src/res/default-user-icon.jpg'}" alt="">`, 'href': `/profile.php?user=${content.author}`}).appendTo(userProfile);
    $('<a/>', {'class': 'username', 'html': content.author, 'href': `/profile.php?user=${content.author}`}).appendTo(userProfile);
    $('<span/>', {'class': 'status', 'html': content.active ? 'Online' : 'Offline'}).appendTo(userProfile);
    userProfile.appendTo(userInfo);
    
    let roleName = 'User';
    switch (content.role) {
        case 'user':
            roleName = 'User';
        break;
        
        case 'mod':
            roleName = 'Moderator';
            break;
            
            case 'siteadmin':
                roleName = 'Site Admin';
        break;
    }
    
    let modStatusText = 'Active';
    switch (content.modStatus) {
        case 1:
            modStatusText = 'Silenced';
            break;
            
        case 2:
            modStatusText = 'Banned';
            break;
    }

    // User Data
    $('<div/>', {'class': 'user-data', 'html': `
        <div><img src="src/res/user-dark.svg" alt=""><span>${roleName}</span></div>
        <div><img src="src/res/pencil.svg" alt=""><span>${content.totalUserPosts} posts</span></div>
        <div><img src="src/res/login.svg" alt=""><span>${formatTimeAsText(content.lastLogin, false)}</span></div>
        <div><img src="src/res/info.svg" alt=""><span>${modStatusText}</span></div>
    `}).appendTo(userInfo);
    userInfo.appendTo(postContent);

    // Post Content
    let postBody = $('<div/>', {'class': 'post-body'});
    let postTitleInput;
    if (type == 0) postTitleInput = $('<input/>', {'class': 'post-title', 'placeholder': 'Write your title here...'}).appendTo(postBody);
    let postEditor = $('<div/>', {'class': 'post-editor'}).ready(function () {
        new Quill('.post-editor', {
            theme: 'snow'
        });

        $(postEditor).children('.ql-editor').html(param5);
    }).appendTo(postBody);

    postBody.appendTo(postContent);
    postContent.appendTo(postWrapper);

    // Post Footer
    let postFooter = $('<div/>', {'class': 'post-footer'});

    $('<button/>', {'html': '<img src="src/res/close.svg" alt="">'}).on('click', () => {

        if (!confirm('Are you sure you want to cancel?')) return;

        switch (type) {
            case 0:
                $('#bottom-seperator h1').html(`Site`);
                $('#bottom-seperator h2').show();
                $('#thread-content').empty();
                break;

            case 1:
                $('#reply-post').remove();
                break;

            case 2:
                $(postWrapper).replaceWith(param1);
                param2.on('click', () => {
                    let editPost = buildCreatePostBuilder('Editing Post', 2, content.id, param1, param2, param3);
                    $(param1).replaceWith(editPost);
            
                })
                break;
        }
    }).appendTo(postFooter);
    
    $('<button/>', {'html': '<img src="src/res/check.svg" alt="">'}).on('click', () => {

        let postContent = $(postEditor).children('.ql-editor').html();

        switch (type) {
            case 0:
                let postTitle = $(postTitleInput).val();
        
                if (postTitle.length < 1 || 
                    postContent.replaceAll(/<[a-zA-Z\/]+>/gm, '').length < 1) {
                    showError('Title or body should not be empty!');
                    return;
                }
        
                let response = createThread(id, postTitle, postContent);
                if (response) 
                    window.location.replace(`/index.php?thread=${response.id}`);
                break;

            case 1:
                if (postContent.replaceAll(/<[a-zA-Z\/]+>/gm, '').length < 1) {
                    showError('Body should not be empty!');
                    return;
                }

                if (createReply(id, postContent)) {
                    window.location.replace(`/index.php?thread=${id}`);
                }
                break;

            case 2:
                if (postContent.replaceAll(/<[a-zA-Z\/]+>/gm, '').length < 1) {
                    showError('Body should not be empty!');
                    return;
                }
                
                if (postEdit(id, param3, postContent)) {
                   window.location.replace(`/index.php?thread=${param4}`);
                }
                break;
        }


    }).appendTo(postFooter);
        
    postFooter.appendTo(postWrapper);
    
    return postWrapper;
}

function getGroups() {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-groups'
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getCategories(groupid=1) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-categories',
            groupid: groupid
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getThreadTitles(categoryid=1) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-thread-titles',
            categoryid: categoryid
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData;
}

function getThreadContent(threadid) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-thread-content',
            threadid: threadid
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            history.replaceState('', '', `/index.php?thread=${threadid}`);
            return responseData;

        case 500 && 404:
            history.replaceState('', '', '/');
            setGroups();
            break;

        case 401:
            window.location.replace('/login.php');
            break;
    }
}

function updatePostLike(id, isLike=false, isThread=false) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'post-like',
            id: id,
            islike: isLike ? 1 : 0,
            isthread: isThread ? 1 : 0
        },
        success: (result) => {
            responseData = result;
        }
    });

    return responseData.response;
}

function getSelfUserData() {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'get-userdata'
        },
        success: (result) => {
            responseData = result;
        }
    });
    
    switch (responseData.response) {
        case 200:
            return responseData.content;

        case 500 && 404:
            history.replaceState('', '', '/');
            setGroups();
            break;

        case 401:
            window.location.replace('/login.php');
            break;
    }
}

function createThread(categoryid, title, content) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'thread-create',
            categoryid : categoryid,
            title: title,
            content: content
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            return responseData;

        case 401:
            window.location.replace('/login.php');
            break;
            
        case 500 && 404 && 403:
            showError('Unable to create thread!');
            return false;
    }

    return false;
}

function createReply(threadid, content) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'thread-reply',
            threadid: threadid,
            content: content
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            return true;

        case 401:
            window.location.replace('/login.php');
            break;
            
        case 500 && 404 && 403:
            showError('Unable to create thread!');
            return false;
    }

    return false;
}

function createReply(threadid, content) {
    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'thread-reply',
            threadid: threadid,
            content: content
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            return true;

        case 401:
            window.location.replace('/login.php');
            break;
            
        case 500 && 404 && 403:
            showError('Unable to create reply!');
            return false;

        case 601:
            showError('You only can create a reply every 1 day');
            return false;
    }

    return false;
}

function postEdit(id, isThread, content) {

    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'post-edit',
            id: id,
            isThread: isThread ? 1 : 0,
            content: content
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            return true;

        case 401:
            window.location.replace('/login.php');
            break;
            
        case 500 && 404 && 403:
            showError('Unable to edit post!');
            return false;

        case 601:
            showError('Time limit exceeded!');
            return false;
    }

    return false;
}

function postDelete(id, isThread) {

    let responseData = null;

    $.post({
        url: 'index.php',
        dataType: 'json',
        async: false,
        data: {
            action: 'post-delete',
            id: id,
            isThread: isThread ? 1 : 0
        },
        success: (result) => {
            responseData = result;
        }
    });

    switch (responseData.response) {
        case 200:
            return true;

        case 401:
            window.location.replace('/login.php');
            break;
            
        case 500 && 404 && 403:
            showError('Unable to delete post!');
            return false;

        case 601:
            showError('Time limit exceeded!');
            return false;
    }

    return false;
}

function formatTimeAsText(time = 0, short=true) {

    // Years
    if (time >= 31536000) {
        time = (time / 31536000) | 0;
        if (time == 1)
            return '1 year ago';

        return `${time} years`;
    }

    // Seconds
    if (time < 60)
        return 'Moments ago';
        
    // Minutes
    time =  (time / 60) | 0;
    if (time < 60)
        if (short) {
            return `${time}m ago`;
        } else {
            if (time == 1)
                return `${time} minute ago`;
            
            return `${time} minutes ago`;
        }

    // Hours
    time =  (time / 60) | 0;
    if (time < 24) {
        if (time == 1)
            return '1 hour ago';

        return `${time} hours ago`;
    }

    // Days
    time =  (time / 24) | 0;
    if (time < 7) {
        if (time == 1)
            return '1 day ago';

        return `${time} days ago`;
    }

    // Weeks
    time =  (time / 7) | 0;
    if (time < 4) {
        if (time == 1)
            return '1 week ago';

        return `${time} weeks ago`;
    }

    // Months
    time = (time / 4) | 0;
    if (time < 12) {
        if (time == 1)
            return '1 month ago';

        return `${time} months ago`;
    }

    return '1 year ago';
}

function convertNumberToShortened(num) {

    if (num >= 1000000000)
        return `${(num / 1000000000).toFixed(1)}B`;

    if (num >= 1000000)
        return `${(num / 1000000).toFixed(1)}M`;

    if (num >= 1000)
        return `${(num / 1000).toFixed(1)}K`;

    return num;
}

$('#error-message button').on('click', function () {
    $('#error-message').hide(300);
});

function showError(message='') {
    $('#error-message span').html(message);
    $('#error-message').show(300);
}