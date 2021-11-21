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
        $('#bottom-seperator h2').remove();

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

        console.log(threadContent);

        $('#thread-content').empty();
        $('#thread-content').append(buildThreadPost('Main Post', threadContent, true))

        let postsContent = result.posts;
        if (postsContent) {
            
            postsContent.forEach(post => {
                $('#thread-content').append(buildThreadPost(post.targetUsername ? `Reply to ${post.targetUsername}` : 'Reply to Main Post', post, false))
            });

        }
    }
}

function buildThreadPost(title, content, isThread=false) {

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
    $('<a/>', {'class': 'avatar', 'html': `<img src="${content.avatarURL ? content.avatarURL : 'src/res/default-user-icon.jpg'}" alt="">`, 'href': `/profile?user=${content.author}`}).appendTo(userProfile);
    $('<a/>', {'class': 'username', 'html': content.author, 'href': `/profile?user=${content.author}`}).appendTo(userProfile);
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
        case 2:
            modStatusText = 'Silenced';
            break;
            
        case 3:
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
    $('<div/>', {'class': 'post-body', 'html': `<p>${content.postBody}</p>`}).appendTo(postContent);
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

    if (!content.isAuthor) {   
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
        
        postButtons.appendTo(postFooter);
        
    postFooter.appendTo(postWrapper);
    
    return postWrapper;
    
}

let content = {
    username: 'blekekok',
    active: 1
};

$('#thread-content').append(buildCreatePostBuilder('Create reply to Main Post', content, false));

function buildCreatePostBuilder(title, content, isThread=false) {

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
    $('<a/>', {'class': 'avatar', 'html': `<img src="${content.avatarURL ? content.avatarURL : 'src/res/default-user-icon.jpg'}" alt="">`, 'href': `/profile?user=${content.author}`}).appendTo(userProfile);
    $('<a/>', {'class': 'username', 'html': content.author, 'href': `/profile?user=${content.author}`}).appendTo(userProfile);
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
        case 2:
            modStatusText = 'Silenced';
            break;
            
        case 3:
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
    $('<div/>', {'class': 'post-body', 'html': `<div class="post-editor"><div id="editor"></div></div>`}).appendTo(postContent);
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

    if (!content.isAuthor) {   
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
        
        postButtons.appendTo(postFooter);
        
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

new Quill('#editor', {
    theme: 'snow'
});