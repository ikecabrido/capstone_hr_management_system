document.addEventListener('DOMContentLoaded', function() {
  const socialFeed = document.getElementById('social-feed');
  if (!socialFeed) return;

  const canReply = socialFeed.dataset.canReply === 'true';
  const employeeId = socialFeed.dataset.employeeId || null;
  var socialPosts = [];
  var sharedFilesData = [];

  function bindCommentForms() {
    const commentForms = document.querySelectorAll('.comment-form');
    commentForms.forEach(function(form) {
      if (form.dataset.bound === 'true') return;
      form.dataset.bound = 'true';
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        const button = form.querySelector('button[type=submit]');
        const textarea = form.querySelector('textarea[name="comment"], textarea[name="content"]');
        const postIdInput = form.querySelector('input[name="post_id"]');
        const commentIdInput = form.querySelector('input[name="comment_id"]');

        if (!textarea || !postIdInput) {
          return;
        }

        const postId = postIdInput.value;
        const payload = {
          post_id: postId,
          content: textarea.value.trim()
        };
        let url = '../api/index.php?resource=social&action=comment';

        if (commentIdInput) {
          const commentId = commentIdInput.value;
          if (!commentId) {
            alert('Comment ID is missing.');
            return;
          }
          payload.comment_id = commentId;
          payload.content = textarea.value.trim();
          url = '../api/index.php?resource=reply&action=add';
        } else {
          payload.comment = textarea.value.trim();
        }

        if (!textarea.value.trim()) {
          alert('Please enter a message.');
          return;
        }

        if (button) {
          button.textContent = 'Posting...';
          button.disabled = true;
        }

        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        })
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            if (data.success || data.id) {
              textarea.value = '';
              fetchSocialFeed();
            } else {
              alert(data.error || data.message || 'Failed to post.');
            }
          })
          .catch(function() {
            alert('Failed to post.');
          })
          .finally(function() {
            if (button) {
              var resetLabel = form.querySelector('input[name="comment_id"]') ? 'Post Comment' : 'Comment';
              button.textContent = resetLabel;
              button.disabled = false;
            }
          });
      });
    });
  }

  function setFeedHtml(html) {
    socialFeed.innerHTML = html;
    bindCommentForms();
  }

  function createPostCard(post) {
    var postId = post.eer_social_post_id || post.id || post.post_id || '';
    postId = postId ? escapeHtml(postId) : '';
    var employeeName = post.author_name ? escapeHtml(post.author_name) : 'Unknown';
    var content = post.content ? escapeHtml(post.content) : '';
    var createdAt = post.created_at ? escapeHtml(post.created_at) : '';
    var commentsHtml = '<p class="text-muted font-italic small mb-0">No comments yet.</p>';

    function renderFileAttachment(attachment) {
      var fileIcon = getFileIcon(attachment.file_type);
      var fileSize = formatFileSize(attachment.file_size);
      var uploaderName = attachment.uploader_name ? escapeHtml(attachment.uploader_name) : 'Unknown';
      var descriptionText = attachment.description ? escapeHtml(attachment.description) : 'No description provided.';
      var fileId = attachment.eer_social_post_id || attachment.id || '';
      var downloadUrl = fileId ? '../download.php?id=' + fileId : '#';

      return '<div class="shared-file-attachment mb-3 p-3 bg-light rounded-lg border">' +
        '<div class="d-flex justify-content-between align-items-start mb-2">' +
          '<div>' +
            '<h6 class="mb-1" style="font-weight:600;"><i class="' + fileIcon + ' mr-2 text-primary"></i>' + escapeHtml(attachment.file_name) + '</h6>' +
            '<span class="badge badge-pill badge-info shared-file-badge">Shared File</span>' +
          '</div>' +
          '<a href="' + downloadUrl + '" class="btn btn-sm btn-outline-primary" download><i class="fas fa-download mr-1"></i>Download</a>' +
        '</div>' +
        '<p class="mb-2 small text-muted">' + descriptionText + '</p>' +
        '<div class="d-flex justify-content-between small text-muted">' +
          '<span>Uploaded by ' + uploaderName + '</span>' +
          '<span>' + escapeHtml(attachment.created_at || '') + '</span>' +
        '</div>' +
      '</div>';
    }

    var fileSection = '';
    if (Array.isArray(post.file_attachments) && post.file_attachments.length > 0) {
      fileSection = post.file_attachments.map(renderFileAttachment).join('');
    } else if (post.file_attachment) {
      fileSection = renderFileAttachment(post.file_attachment);
    }

    var likeCount = post.like_count ? parseInt(post.like_count, 10) : 0;
    var heartCount = post.heart_count ? parseInt(post.heart_count, 10) : 0;
    var wowCount = post.wow_count ? parseInt(post.wow_count, 10) : 0;

    var reactionHtml = (likeCount > 0 ? '<i class="fas fa-thumbs-up text-primary mr-1"></i>' + likeCount + ' ' : '') +
           (heartCount > 0 ? '<i class="fas fa-heart text-danger mr-1"></i>' + heartCount + ' ' : '') +
           (wowCount > 0 ? '<i class="fas fa-star text-warning mr-1"></i>' + wowCount + ' ' : '');
    
    if (!reactionHtml.trim()) {
      reactionHtml = '<span class="text-muted">No reactions yet</span>';
    }

    if (Array.isArray(post.comments) && post.comments.length > 0) {
      commentsHtml = '<div class="comments-list">' +
        post.comments.map(function(comment) {
          var commenter = comment.author_name ? escapeHtml(comment.author_name) : 'Unknown';
          var commentText = comment.comment ? escapeHtml(comment.comment) : '';
          var commentTime = comment.created_at ? escapeHtml(comment.created_at) : '';
          var repliesHtml = '';

          if (Array.isArray(comment.replies) && comment.replies.length > 0) {
            repliesHtml = '<div class="replies mt-2 ml-4 border-left pl-2">' +
              comment.replies.map(function(reply) {
                var replier = reply.author_name ? escapeHtml(reply.author_name) : 'Unknown';
                var replyText = reply.content ? escapeHtml(reply.content) : '';
                var replyTime = reply.created_at ? escapeHtml(reply.created_at) : '';
                return '<div class="reply-item mb-2"><strong class="small">' + replier + ':</strong> <span class="small">' + replyText + '</span> <small class="text-muted d-block">' + replyTime + '</small></div>';
              }).join('') +
              '</div>';
          }

          return '<div class="comment-item mb-3 pb-2 border-bottom">' +
            '<div><strong class="small">' + commenter + ':</strong> <span class="small">' + commentText + '</span> </div>' +
            '<small class="text-muted d-block mb-2">' + commentTime + '</small>' +
            repliesHtml +
            '<div class="reply-actions mt-2">' +
              '<button type="button" class="btn btn-sm btn-link reply-action p-0" data-comment-id="' + comment.eer_comment_id + '" data-post-id="' + postId + '" style="color: #007bff;"><small>Reply</small></button>' +
            '</div>' +
            '<form method="POST" class="comment-form reply-form mt-2 p-2 bg-light rounded d-none" data-comment-id="' + comment.eer_comment_id + '" data-post-id="' + postId + '">' +
              '<input type="hidden" name="comment_id" value="' + comment.eer_comment_id + '">' +
              '<input type="hidden" name="post_id" value="' + postId + '">' +
              '<div class="form-group mb-2">' +
                '<textarea name="content" class="form-control form-control-sm" rows="2" placeholder="Write your reply..." required></textarea>' +
              '</div>' +
              '<button type="submit" class="btn btn-sm btn-primary">Post Reply</button>' +
              '<button type="button" class="btn btn-sm btn-secondary ms-2 cancel-reply" style="margin-left: 0.5rem;">Cancel</button>' +
            '</form>' +
            '</div>';
        }).join('') +
        '</div>';
    }

    var replySection = '<form method="POST" class="comment-form mt-3">' +
      '<input type="hidden" name="post_id" value="' + postId + '">' +
      '<div class="form-group mb-2">' +
        '<textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Write a comment..." required></textarea>' +
      '</div>' +
      '<button type="submit" class="btn btn-sm btn-primary">Comment</button>' +
      '</form>';

    var reactionButtons = '<div class="reaction-buttons mt-3 d-flex gap-2">' +
      '<button type="button" class="btn btn-sm btn-outline-primary react-btn" data-post-id="' + postId + '" data-reaction="like"><i class="fas fa-thumbs-up mr-1"></i>Like <span class="reaction-count" data-reaction="like">' + likeCount + '</span></button>' +
      '<button type="button" class="btn btn-sm btn-outline-danger react-btn" data-post-id="' + postId + '" data-reaction="heart"><i class="fas fa-heart mr-1"></i>Heart <span class="reaction-count" data-reaction="heart">' + heartCount + '</span></button>' +
      '<button type="button" class="btn btn-sm btn-outline-warning react-btn" data-post-id="' + postId + '" data-reaction="wow"><i class="fas fa-star mr-1"></i>Wow <span class="reaction-count" data-reaction="wow">' + wowCount + '</span></button>' +
      '</div>';

    return '<div class="card mb-3 social-post-card" style="border-left: 4px solid #007bff;">' +
      '<div class="card-body">' +
        '<div class="post-header d-flex justify-content-between align-items-start mb-3">' +
          '<div>' +
            '<h6 class="card-title mb-1" style="font-weight: 600;">' + employeeName + '</h6>' +
            '<small class="text-muted">' + createdAt + '</small>' +
          '</div>' +
        '</div>' +
        '<p class="card-text mb-3 lh-relaxed">' + content + '</p>' +
        fileSection +
        '<div class="reaction-summary border-top border-bottom py-2 px-0 mb-3">' +
          '<small class="text-muted">' + reactionHtml + '</small>' +
        '</div>' +
        reactionButtons +
        '<div class="mt-3">' +
          '<div class="comments-section">' + commentsHtml + '</div>' +
          replySection +
        '</div>' +
      '</div>' +
      '</div>';
  }

  function createSharedFileCard(file) {
    var fileId = file.eer_social_post_id || file.eer_shared_file_id || file.id || file.file_id || '';
    fileId = fileId ? fileId : '';  // Don't escape - we need the raw ID for download link
    var fileIcon = getFileIcon(file.file_type);
    var fileSize = formatFileSize(file.file_size);
    var uploaderName = file.uploader_name || 'Unknown';
    var description = file.description || '';
    var content = file.content ? String(file.content).trim() : '';
    var createdAt = file.created_at || '';

    var contentHtml = content ? '<p class="card-text mb-3">' + escapeHtml(content) + '</p>' : '';
    var descriptionHtml = '';
    if (description && description !== content) {
      descriptionHtml = '<p class="card-text text-clamp-3">' + escapeHtml(description) + '</p>';
    } else if (!content) {
      descriptionHtml = '<p class="card-text text-clamp-3">No description provided.</p>';
    }

    var likeCount = file.like_count ? parseInt(file.like_count, 10) : 0;
    var heartCount = file.heart_count ? parseInt(file.heart_count, 10) : 0;
    var wowCount = file.wow_count ? parseInt(file.wow_count, 10) : 0;

    var reactionHtml = (likeCount > 0 ? '<i class="fas fa-thumbs-up text-primary mr-1"></i>' + likeCount + ' ' : '') +
           (heartCount > 0 ? '<i class="fas fa-heart text-danger mr-1"></i>' + heartCount + ' ' : '') +
           (wowCount > 0 ? '<i class="fas fa-star text-warning mr-1"></i>' + wowCount + ' ' : '');
    
    if (!reactionHtml.trim()) {
      reactionHtml = '<span class="text-muted">No reactions yet</span>';
    }

    var reactionButtons = '<div class="reaction-buttons mt-2 d-flex gap-2">' +
      '<button type="button" class="btn btn-sm btn-outline-primary react-btn" data-post-id="' + fileId + '" data-reaction="like"><i class="fas fa-thumbs-up mr-1"></i>Like <span class="reaction-count" data-reaction="like">' + likeCount + '</span></button>' +
      '<button type="button" class="btn btn-sm btn-outline-danger react-btn" data-post-id="' + fileId + '" data-reaction="heart"><i class="fas fa-heart mr-1"></i>Heart <span class="reaction-count" data-reaction="heart">' + heartCount + '</span></button>' +
      '<button type="button" class="btn btn-sm btn-outline-warning react-btn" data-post-id="' + fileId + '" data-reaction="wow"><i class="fas fa-star mr-1"></i>Wow <span class="reaction-count" data-reaction="wow">' + wowCount + '</span></button>' +
      '</div>';

    var commentHtml = '<form method="POST" class="comment-form mt-2">' +
      '<input type="hidden" name="post_id" value="' + fileId + '">' +
      '<div class="form-group mb-2">' +
        '<textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Write a comment..." required></textarea>' +
      '</div>' +
      '<button type="submit" class="btn btn-sm btn-primary">Comment</button>' +
      '</form>';

    return '<div class="card mb-3 shared-file-card" style="border-left: 4px solid #17a2b8;"><div class="card-body">' +
      '<div class="post-header mb-2 align-items-start">' +
        '<div>' +
          '<h5 class="card-title mb-1"><i class="' + fileIcon + ' mr-2 text-primary"></i>' + escapeHtml(file.file_name) + '</h5>' +
          '<span class="badge badge-pill badge-info shared-file-badge">Shared File</span>' +
        '</div>' +
        '<div class="post-timestamp text-muted small">' + escapeHtml(createdAt) + '</div>' +
      '</div>' +
      contentHtml +
      descriptionHtml +
      '<div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">' +
        '<div class="text-muted small">Uploaded by ' + escapeHtml(uploaderName) + ' • ' + fileSize + '</div>' +
        '<a href="../download.php?id=' + fileId + '" class="btn btn-sm btn-outline-primary" download><i class="fas fa-download"></i> Download</a>' +
      '</div>' +
      '<div class="reaction-summary border-top border-bottom py-2 px-0 mt-3 mb-3">' +
        '<small class="text-muted">' + reactionHtml + '</small>' +
      '</div>' +
      reactionButtons +
      commentHtml +
      '</div></div>';
  }

  function renderCombinedFeed() {
    var items = [];
    var mergedPosts = Array.isArray(socialPosts) ? socialPosts.slice() : [];
    mergedPosts = mergedPosts.filter(function(post) {
      return !post.item_type || post.item_type === 'post';
    });

    if (Array.isArray(sharedFilesData) && sharedFilesData.length > 0) {
      sharedFilesData.forEach(function(file) {
        var matchedPost = null;
        
        // Try to find a matching post
        for (var i = 0; i < mergedPosts.length; i++) {
          var post = mergedPosts[i];
          if (!post.created_at || !file.created_at) continue;

          // Check author match
          var postUserId = String(post.user_id || post.employee_id || '').trim();
          var fileUserId = String(file.user_id || file.employee_id || '').trim();
          var postUserType = String(post.user_type || 'user').trim().toLowerCase();
          var fileUserType = String(file.user_type || 'user').trim().toLowerCase();
          
          var sameAuthorId = postUserId && fileUserId && postUserId === fileUserId;
          var sameAuthorName = false;
          if (!sameAuthorId) {
            var postAuthorName = String(post.author_name || post.uploader_name || '').trim().toLowerCase();
            var fileAuthorName = String(file.uploader_name || file.author_name || '').trim().toLowerCase();
            sameAuthorName = postAuthorName && fileAuthorName && postAuthorName === fileAuthorName;
          }
          
          if (!sameAuthorId && !sameAuthorName) continue;

          // Check time window (90 seconds to be more forgiving)
          var postTime = new Date(post.created_at).getTime();
          var fileTime = new Date(file.created_at).getTime();
          var timeDiff = Math.abs(postTime - fileTime);
          if (timeDiff > 90000) continue;

          matchedPost = post;
          break;
        }

        if (matchedPost) {
          matchedPost.file_attachments = matchedPost.file_attachments || [];
          matchedPost.file_attachments.push(file);
          file.__merged = true;
        } else {
          file.__feed_type = 'file';
          items.push(file);
        }
      });
    }

    if (mergedPosts.length > 0) {
      mergedPosts.forEach(function(post) {
        post.__feed_type = 'post';
        items.push(post);
      });
    }

    if (items.length === 0) {
      setFeedHtml('<p class="text-muted">No posts or shared files yet.</p>');
      return;
    }

    items.sort(function(a, b) {
      var dateA = new Date(a.created_at || a.uploaded_at || 0).getTime();
      var dateB = new Date(b.created_at || b.uploaded_at || 0).getTime();
      if (dateA !== dateB) {
        return dateB - dateA;
      }
      if (a.__feed_type === b.__feed_type) {
        return 0;
      }
      return a.__feed_type === 'file' ? -1 : 1;
    });

    var html = items.map(function(item) {
      return item.__feed_type === 'file' ? createSharedFileCard(item) : createPostCard(item);
    }).join('');

    setFeedHtml(html);
    updateAnalytics(items);
  }

  function updateReactionCount(postId, reactionType, increment) {
    var reactionButton = document.querySelector('.react-btn[data-post-id="' + postId + '"][data-reaction="' + reactionType + '"]');
    if (!reactionButton) {
      fetchSocialFeed();
      return;
    }
    var reactionCountSpan = reactionButton.querySelector('.reaction-count[data-reaction="' + reactionType + '"]');
    if (!reactionCountSpan) {
      fetchSocialFeed();
      return;
    }
    var currentCount = parseInt(reactionCountSpan.textContent, 10) || 0;
    reactionCountSpan.textContent = Math.max(0, currentCount + (increment ? 1 : -1));
  }

  socialFeed.addEventListener('click', function(event) {
    // Handle Reply button click
    var replyToggle = event.target.closest('.reply-action');
    if (replyToggle) {
      event.preventDefault();
      var commentId = replyToggle.getAttribute('data-comment-id');
      var postId = replyToggle.getAttribute('data-post-id');
      var form = socialFeed.querySelector('.reply-form[data-comment-id="' + commentId + '"][data-post-id="' + postId + '"]');
      if (form) {
        form.classList.toggle('d-none');
      }
      return;
    }

    // Handle Cancel button click
    var cancelBtn = event.target.closest('.cancel-reply');
    if (cancelBtn) {
      event.preventDefault();
      var form = cancelBtn.closest('.reply-form');
      if (form) {
        form.classList.add('d-none');
      }
      return;
    }

    var button = event.target.closest('.react-btn');
    if (!button) return;

    var postId = button.getAttribute('data-post-id');
    var reactionType = button.getAttribute('data-reaction');

    var payload = { post_id: postId, type: reactionType };
    if (employeeId) {
      payload.employee_id = employeeId;
    }

    fetch('../api/index.php?resource=reaction', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function(data) {
        if (data.success) {
          fetchSocialFeed();
        } else {
          alert(data.message || 'Failed to react.');
        }
      })
      .catch(function() {
        alert('Failed to send reaction.');
      });
  });

  function fetchSocialFeed() {
    var feedPromise = fetch('../api/index.php?resource=social&action=feed', {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Failed to load social posts.');
        }
        return response.json();
      })
      .then(function(data) {
        if (data && data.success && Array.isArray(data.data)) {
          socialPosts = data.data;
        } else {
          socialPosts = [];
        }
      })
      .catch(function() {
        socialPosts = [];
      });

    var filePromise = fetch('../api/index.php?resource=shared_files&action=list', {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Failed to load shared files.');
        }
        return response.json();
      })
      .then(function(data) {
        if (data && data.success && Array.isArray(data.data)) {
          sharedFilesData = data.data;
        } else {
          sharedFilesData = [];
        }
      })
      .catch(function() {
        sharedFilesData = [];
      });

    Promise.all([feedPromise, filePromise])
      .then(function() {
        renderCombinedFeed();
      })
      .catch(function() {
        setFeedHtml('<p class="text-danger">Failed to load social feed.</p>');
        updateAnalytics([]);
      });
  }

  function computeSentimentSummary(posts) {
    if (!Array.isArray(posts) || posts.length === 0) {
      return { positive: 0, neutral: 0, negative: 0 };
    }

    var positiveWords = ['good','great','love','excellent','awesome','happy','nice','amazing'];
    var negativeWords = ['bad','sad','angry','terrible','hate','poor','worst','problem'];

    var counts = { positive: 0, neutral: 0, negative: 0 };

    posts.forEach(function(post) {
      var content = (post.content || '') + ' ' + (Array.isArray(post.comments) ? post.comments.map(function(c){ return c.comment || ''; }).join(' ') : '');
      var text = content.toLowerCase();

      var foundPositive = positiveWords.some(function(word){ return text.indexOf(word) !== -1; });
      var foundNegative = negativeWords.some(function(word){ return text.indexOf(word) !== -1; });

      if (foundPositive && !foundNegative) {
        counts.positive++;
      } else if (foundNegative && !foundPositive) {
        counts.negative++;
      } else {
        counts.neutral++;
      }
    });

    return counts;
  }

  function updateAnalytics(posts) {
    var totalPosts = Array.isArray(posts) ? posts.length : 0;
    var totalComments = 0;
    var totalReactions = 0;

    if (Array.isArray(posts)) {
      posts.forEach(function(post) {
        totalComments += Array.isArray(post.comments) ? post.comments.length : 0;
        totalReactions += (parseInt(post.like_count,10)||0) + (parseInt(post.heart_count,10)||0) + (parseInt(post.wow_count,10)||0);
      });
    }

    var sentiment = computeSentimentSummary(posts);

    var engagementHtml = '<div class="row">'
      + '<div class="col-sm-4"><strong>Total posts:</strong> ' + totalPosts + '</div>'
      + '<div class="col-sm-4"><strong>Total comments:</strong> ' + totalComments + '</div>'
      + '<div class="col-sm-4"><strong>Total reactions:</strong> ' + totalReactions + '</div>'
      + '</div>';

    document.getElementById('engagement-analytics').innerHTML = engagementHtml;

    var sentimentHtml = '<div class="row">'
      + '<div class="col-sm-4"><strong>Positive:</strong> ' + sentiment.positive + '</div>'
      + '<div class="col-sm-4"><strong>Neutral:</strong> ' + sentiment.neutral + '</div>'
      + '<div class="col-sm-4"><strong>Negative:</strong> ' + sentiment.negative + '</div>'
      + '</div>';

    document.getElementById('sentiment-analysis').innerHTML = sentimentHtml;
  }

  function escapeHtml(text) {
    var span = document.createElement('span');
    span.textContent = text;
    return span.innerHTML;
  }

  function renderGroupMembers(groupId, members) {
    var wrapper = document.getElementById('group-members-' + groupId);
    if (!wrapper) return;

    if (!Array.isArray(members) || members.length === 0) {
      wrapper.innerHTML = '<p class="text-muted mb-0">No members yet.</p>';
      return;
    }

    var items = members.map(function(member) {
      var text = 'Employee ID: ' + escapeHtml(member.employee_id || 'N/A');
      if (member.full_name) {
        text += ' - ' + escapeHtml(member.full_name);
      }
      return '<li class="list-group-item py-1">' + text + '</li>';
    }).join('');

    wrapper.innerHTML = '<ul class="list-group list-group-flush">' + items + '</ul>';
  }

  function refreshGroupMembers(groupId) {
    fetch('../api/index.php?resource=group_member&action=list&group_id=' + encodeURIComponent(groupId), {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
      .then(function(response) {
        if (response.ok) {
          return response.json();
        }
        if (response.status === 404) {
          return { success: false, noMembers: true };
        }
        throw new Error('Failed to refresh group members.');
      })
      .then(function(data) {
        if (data.success && Array.isArray(data.data)) {
          renderGroupMembers(groupId, data.data);
        } else if (data.noMembers) {
          renderGroupMembers(groupId, []);
        }
      })
      .catch(function() {
        console.warn('Unable to refresh group members for group', groupId);
      });
  }

  var shareStatus = document.getElementById('share-status');
  var shareForm = document.querySelector('.share-form');

  function showShareStatus(message, type) {
    if (!shareStatus) return;
    shareStatus.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
  }

  if (shareForm) {
    shareForm.addEventListener('submit', function(event) {
      event.preventDefault();
      event.stopImmediatePropagation();
      if (!shareStatus) return;

      var contentInput = document.getElementById('content');
      var fileInput = document.getElementById('file-upload');
      var descriptionInput = document.getElementById('file-description');
      var content = contentInput ? contentInput.value.trim() : '';
      var description = descriptionInput ? descriptionInput.value.trim() : '';
      var file = fileInput && fileInput.files.length > 0 ? fileInput.files[0] : null;

      if (!content && !file) {
        showShareStatus('Please add a message or attach a file before sharing.', 'danger');
        return;
      }

      shareForm.querySelector('button[type="submit"]').textContent = 'Sharing...';
      shareForm.querySelector('button[type="submit"]').disabled = true;
      showShareStatus('Sending your update...', 'info');

      var postPromise = Promise.resolve({ success: true });
      var filePromise = Promise.resolve({ success: true });

      if (file) {
        if (!description && content) {
          description = content;
        }
        var formData = new FormData();
        formData.append('shared_file', file);
        if (description) {
          formData.append('description', description);
        }
        if (content) {
          formData.append('content', content);
        }
        filePromise = fetch('../api/index.php?resource=file_sharing', {
          method: 'POST',
          body: formData
        }).then(function(response) {
          return response.json().then(function(data) {
            if (!response.ok) {
              throw new Error(data.message || 'Upload failed');
            }
            return data;
          });
        });
      } else if (content) {
        postPromise = fetch('../api/index.php?resource=social&action=post', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ content: content })
        }).then(function(response) {
          return response.json();
        });
      }

      Promise.all([postPromise, filePromise])
        .then(function(results) {
          var postResult = results[0];
          var fileResult = results[1];
          if ((postResult && !postResult.success) || (fileResult && !fileResult.success)) {
            var message = (postResult && postResult.message) || (fileResult && fileResult.message) || 'Failed to share update.';
            showShareStatus(message, 'danger');
            return;
          }

          showShareStatus('Your update has been shared.', 'success');
          if (contentInput) contentInput.value = '';
          if (fileInput) fileInput.value = '';
          if (descriptionInput) descriptionInput.value = '';
          fetchSocialFeed();
        })
        .catch(function(error) {
          showShareStatus(error.message || 'Unable to share your update.');
        })
        .finally(function() {
          if (shareForm) {
            var btn = shareForm.querySelector('button[type="submit"]');
            if (btn) {
              btn.textContent = 'Share';
              btn.disabled = false;
            }
          }
        });
    });
  }

  var groupMemberForm = document.getElementById('group-member-form');
  if (groupMemberForm) {
    groupMemberForm.addEventListener('submit', function(event) {
      event.preventDefault();

      var groupId = document.getElementById('group-id').value;
      var employeeId = document.getElementById('employee-id').value;

      if (!groupId || !employeeId) {
        alert('Please select a group and an employee.');
        return;
      }

      fetch('../api/index.php?resource=group_member', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          group_id: groupId,
          employee_id: employeeId
        })
      })
        .then(function(response) {
          return response.json().then(function(data) {
            if (!response.ok) {
              throw new Error(data.message || 'Failed to add member.');
            }
            return data;
          });
        })
        .then(function(data) {
          if (data.success) {
            refreshGroupMembers(groupId);
          } else {
            alert(data.message || 'Failed to add member.');
          }
        })
        .catch(function(error) {
          alert(error.message || 'Failed to add member.');
        });
    });
  }

  var createForumForm = document.getElementById('createForumForm');
  if (createForumForm) {
    createForumForm.addEventListener('submit', function(event) {
      event.preventDefault();
      var titleInput = document.getElementById('forumTitle');
      var descriptionInput = document.getElementById('forumDescription');
      var categoryInput = document.getElementById('forumCategory');
      var title = titleInput ? titleInput.value.trim() : '';
      var description = descriptionInput ? descriptionInput.value.trim() : '';
      var category = categoryInput ? categoryInput.value : '';

      if (!title || !description || !category) {
        alert('Please fill out all forum fields.');
        return;
      }

      var submitButton = createForumForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.textContent = 'Creating...';
        submitButton.disabled = true;
      }

      fetch('../api/index.php?resource=forum&action=create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: title, description: description, category: category })
      })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            if (titleInput) titleInput.value = '';
            if (descriptionInput) descriptionInput.value = '';
            if (categoryInput) categoryInput.value = '';
            $('#createForumModal').modal('hide');
            loadForums();
          } else {
            alert(data.message || 'Failed to create forum.');
          }
        })
        .catch(function() {
          alert('Unable to create forum.');
        })
        .finally(function() {
          if (submitButton) {
            submitButton.textContent = 'Create Forum';
            submitButton.disabled = false;
          }
        });
    });
  }

  var createProjectForm = document.getElementById('createProjectForm');
  if (createProjectForm) {
    createProjectForm.addEventListener('submit', function(event) {
      event.preventDefault();
      var nameInput = document.getElementById('projectName');
      var descriptionInput = document.getElementById('projectDescription');
      var deadlineInput = document.getElementById('projectDeadline');
      var statusInput = document.getElementById('projectStatus');
      var name = nameInput ? nameInput.value.trim() : '';
      var description = descriptionInput ? descriptionInput.value.trim() : '';
      var deadline = deadlineInput ? deadlineInput.value : '';
      var status = statusInput ? statusInput.value : '';

      if (!name || !description) {
        alert('Please provide a project name and description.');
        return;
      }

      var submitButton = createProjectForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.textContent = 'Creating...';
        submitButton.disabled = true;
      }

      fetch('../api/index.php?resource=project&action=create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: name,
          description: description,
          deadline: deadline,
          status: status
        })
      })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            if (nameInput) nameInput.value = '';
            if (descriptionInput) descriptionInput.value = '';
            if (deadlineInput) deadlineInput.value = '';
            if (statusInput) statusInput.value = 'planning';
            $('#createProjectModal').modal('hide');
            loadProjects();
          } else {
            alert(data.message || 'Failed to create project.');
          }
        })
        .catch(function() {
          alert('Unable to create project.');
        })
        .finally(function() {
          if (submitButton) {
            submitButton.textContent = 'Create Project Space';
            submitButton.disabled = false;
          }
        });
    });
  }

  function loadForums() {
    fetch('../api/index.php?resource=forum&action=list', {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        if (data.success && Array.isArray(data.data)) {
          renderForums(data.data);
        } else {
          document.getElementById('forums-list').innerHTML = '<div class="alert alert-info">No forums available yet.</div>';
        }
      })
      .catch(function() {
        document.getElementById('forums-list').innerHTML = '<div class="alert alert-danger">Failed to load forums.</div>';
      });
  }

  function renderForums(forums) {
    var container = document.getElementById('forums-list');
    if (!container) return;

    if (!Array.isArray(forums) || forums.length === 0) {
      container.innerHTML = '<div class="alert alert-info">No forums available yet.</div>';
      return;
    }

    var html = forums.map(function(forum) {
      return '<div class="card mb-3 forum-card">' +
        '<div class="card-body">' +
        '<div class="d-flex justify-content-between align-items-start mb-2">' +
        '<div>' +
        '<h5 class="mb-1" style="font-weight:600;">' + escapeHtml(forum.title || 'Untitled Forum') + '</h5>' +
        '<p class="mb-1 text-muted">' + escapeHtml(forum.description || '') + '</p>' +
        '<small class="text-muted">Category: ' + escapeHtml(forum.category || 'General') + '</small>' +
        '</div>' +
        '<button type="button" class="btn btn-sm btn-outline-primary view-forum-btn" data-forum-id="' + escapeHtml(forum.eer_forum_id || forum.id || '') + '">View</button>' +
        '</div>' +
        '<div class="d-flex justify-content-between text-muted small">' +
        '<span>Created by: ' + escapeHtml(forum.creator_name || forum.created_by_user_id || 'Unknown') + '</span>' +
        '<span>' + escapeHtml(forum.created_at || '') + '</span>' +
        '</div>' +
        '</div>' +
        '</div>';
    }).join('');

    container.innerHTML = html;
    bindForumButtons();
  }

  function bindForumButtons() {
    var buttons = document.querySelectorAll('.view-forum-btn');
    buttons.forEach(function(button) {
      if (button.dataset.bound === 'true') return;
      button.dataset.bound = 'true';
      button.addEventListener('click', function() {
        var forumId = button.getAttribute('data-forum-id');
        if (!forumId) return;
        fetch('../api/index.php?resource=forum&action=get&id=' + encodeURIComponent(forumId), {
          method: 'GET',
          headers: { 'Accept': 'application/json' }
        })
          .then(function(response) { return response.json(); })
          .then(function(data) {
            if (data.success && data.data) {
              openForumModal(data.data);
            } else {
              alert(data.message || 'Failed to load forum details.');
            }
          })
          .catch(function() {
            alert('Failed to load forum details.');
          });
      });
    });
  }

  function openForumModal(forum) {
    var title = document.getElementById('forumViewTitle');
    var description = document.getElementById('forumViewDescription');
    var category = document.getElementById('forumViewCategory');
    var createdBy = document.getElementById('forumViewCreatedBy');
    var createdAt = document.getElementById('forumViewCreatedAt');

    if (title) title.textContent = forum.title || 'Untitled Forum';
    if (description) description.textContent = forum.description || '';
    if (category) category.textContent = forum.category || 'General';
    if (createdBy) createdBy.textContent = forum.creator_name || forum.created_by_user_id || 'Unknown';
    if (createdAt) createdAt.textContent = forum.created_at || '';

    $('#viewForumModal').modal('show');
  }

  $('#collaboration-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr('href');
    if (target === '#forums') {
      loadForums();
    } else if (target === '#projects') {
      loadProjects();
    }
  });

  function loadProjects() {
    fetch('../api/index.php?resource=project&action=list', {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        if (data.success && Array.isArray(data.data)) {
          renderProjects(data.data);
        } else {
          document.getElementById('projects-list').innerHTML = '<div class="alert alert-info">No project spaces available yet.</div>';
        }
      })
      .catch(function() {
        document.getElementById('projects-list').innerHTML = '<div class="alert alert-danger">Failed to load project spaces.</div>';
      });
  }

  function renderProjects(projects) {
    var container = document.getElementById('projects-list');
    if (!container) return;

    if (!Array.isArray(projects) || projects.length === 0) {
      container.innerHTML = '<div class="alert alert-info">No project spaces available yet.</div>';
      return;
    }

    var html = projects.map(function(project) {
      var statusBadge = '<span class="badge badge-secondary">' + escapeHtml(project.status || 'Unknown') + '</span>';
      if (project.status === 'active') {
        statusBadge = '<span class="badge badge-success">Active</span>';
      } else if (project.status === 'completed') {
        statusBadge = '<span class="badge badge-primary">Completed</span>';
      } else if (project.status === 'on-hold') {
        statusBadge = '<span class="badge badge-warning">On Hold</span>';
      } else if (project.status === 'planning') {
        statusBadge = '<span class="badge badge-info">Planning</span>';
      }

      return '<div class="card mb-3 project-card">'
        + '<div class="card-body">'
        + '<div class="d-flex justify-content-between align-items-start mb-2">'
        + '<div>'
        + '<h5 class="mb-1" style="font-weight:600;">' + escapeHtml(project.name || 'Untitled Project') + '</h5>'
        + '<p class="mb-1 text-muted">' + escapeHtml(project.description || '') + '</p>'
        + '<div class="d-flex flex-wrap align-items-center">'
        + statusBadge
        + '<small class="text-muted ml-3">Deadline: ' + escapeHtml(project.deadline || 'Not set') + '</small>'
        + '</div>'
        + '</div>'
        + '<button type="button" class="btn btn-sm btn-outline-primary view-project-btn" data-project-id="' + escapeHtml(project.eer_project_id || project.id || '') + '">View</button>'
        + '</div>'
        + '<div class="d-flex justify-content-between text-muted small">'
        + '<span>Created by: ' + escapeHtml(project.creator_name || project.created_by_user_id || 'Unknown') + '</span>'
        + '<span>' + escapeHtml(project.created_at || '') + '</span>'
        + '</div>'
        + '</div>'
        + '</div>';
    }).join('');

    container.innerHTML = html;
    bindProjectButtons();
  }

  function bindProjectButtons() {
    var buttons = document.querySelectorAll('.view-project-btn');
    buttons.forEach(function(button) {
      if (button.dataset.bound === 'true') return;
      button.dataset.bound = 'true';
      button.addEventListener('click', function() {
        var projectId = button.getAttribute('data-project-id');
        if (!projectId) return;

        fetch('../api/index.php?resource=project&action=get&id=' + encodeURIComponent(projectId), {
          method: 'GET',
          headers: { 'Accept': 'application/json' }
        })
          .then(function(response) { return response.json(); })
          .then(function(data) {
            if (data.success && data.data) {
              openProjectModal(data.data);
            } else {
              alert(data.message || 'Failed to load project details.');
            }
          })
          .catch(function() {
            alert('Failed to load project details.');
          });
      });
    });
  }

  function openProjectModal(project) {
    var name = document.getElementById('projectViewName');
    var description = document.getElementById('projectViewDescription');
    var status = document.getElementById('projectViewStatus');
    var deadline = document.getElementById('projectViewDeadline');
    var createdBy = document.getElementById('projectViewCreatedBy');
    var createdAt = document.getElementById('projectViewCreatedAt');

    if (name) name.textContent = project.name || 'Untitled Project';
    if (description) description.textContent = project.description || '';
    if (status) status.textContent = project.status ? project.status.replace(/-/g, ' ') : 'Unknown';
    if (deadline) deadline.textContent = project.deadline || 'Not set';
    if (createdBy) createdBy.textContent = project.creator_name || project.created_by_user_id || 'Unknown';
    if (createdAt) createdAt.textContent = project.created_at || '';

    $('#viewProjectModal').modal('show');
  }

  function fetchSharedFiles() {
    fetch('../api/index.php?resource=shared_files&action=list', {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function(data) {
        if (data && data.success && Array.isArray(data.data)) {
          renderSharedFiles(data.data);
        } else {
          document.getElementById('shared-files-list').innerHTML = '<p class="text-muted">No shared files yet.</p>';
        }
      })
      .catch(function() {
        document.getElementById('shared-files-list').innerHTML = '<p class="text-danger">Failed to load shared files.</p>';
      });
  }

  function renderSharedFiles(files) {
    if (!Array.isArray(files) || files.length === 0) {
      document.getElementById('shared-files-list').innerHTML = '<p class="text-muted">No shared files yet.</p>';
      return;
    }

    var html = '<div class="list-group">';
    files.forEach(function(file) {
      var fileIcon = getFileIcon(file.file_type);
      var fileSize = formatFileSize(file.file_size);
      var uploaderName = file.uploader_name || 'Unknown';
      var description = file.description || '';
      var createdAt = file.created_at || '';

      html += '<div class="list-group-item">'
        + '<div class="d-flex w-100 justify-content-between">'
        + '<div class="d-flex align-items-center">'
        + '<i class="' + fileIcon + ' mr-3 text-primary" style="font-size: 24px;"></i>'
        + '<div>'
        + '<h6 class="mb-1">' + escapeHtml(file.file_name) + '</h6>'
        + '<p class="mb-1 text-muted">' + escapeHtml(description) + '</p>'
        + '<small class="text-muted">Uploaded by ' + escapeHtml(uploaderName) + ' on ' + escapeHtml(createdAt) + ' • ' + fileSize + '</small>'
        + '</div>'
        + '</div>'
        + '<div class="d-flex align-items-center">'
        + '<a href="' + escapeHtml(file.file_path) + '" class="btn btn-sm btn-outline-primary" download><i class="fas fa-download"></i> Download</a>'
        + '</div>'
        + '</div>'
        + '</div>';
    });
    html += '</div>';

    document.getElementById('shared-files-list').innerHTML = html;
  }

  function getFileIcon(fileType) {
    var icons = {
      'pdf': 'fas fa-file-pdf',
      'doc': 'fas fa-file-word',
      'docx': 'fas fa-file-word',
      'xls': 'fas fa-file-excel',
      'xlsx': 'fas fa-file-excel',
      'txt': 'fas fa-file-alt',
      'jpg': 'fas fa-file-image',
      'jpeg': 'fas fa-file-image',
      'png': 'fas fa-file-image'
    };
    return icons[fileType] || 'fas fa-file';
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  fetchSocialFeed();
  loadForums();
  loadProjects();
});
