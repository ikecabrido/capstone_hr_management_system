document.addEventListener('DOMContentLoaded', function() {
  const socialFeed = document.getElementById('social-feed');
  if (!socialFeed) return;

  const canReply = socialFeed.dataset.canReply === 'true';
  const employeeId = socialFeed.dataset.employeeId || null;

  const commentForms = document.querySelectorAll('.comment-form');
  commentForms.forEach(function(form) {
    form.addEventListener('submit', function() {
      const button = form.querySelector('button[type=submit]');
      if (button) { button.textContent = 'Posting...'; button.disabled = true; }
    });
  });

  function setFeedHtml(html) {
    socialFeed.innerHTML = html;
  }

  function renderSocialFeed(posts) {
    if (!Array.isArray(posts) || posts.length === 0) {
      setFeedHtml('<p class="text-muted">No posts yet.</p>');
      return;
    }

    var html = posts.map(function(post) {
      var postId = post.eer_social_post_id || post.id || post.post_id || '';
      postId = postId ? escapeHtml(postId) : '';
      var employeeName = post.author_name ? escapeHtml(post.author_name) : 'Unknown';
      var content = post.content ? escapeHtml(post.content) : '';
      var createdAt = post.created_at ? escapeHtml(post.created_at) : '';
      var commentsHtml = '<p class="text-muted">No comments yet.</p>';

      var likeCount = post.like_count ? parseInt(post.like_count, 10) : 0;
      var heartCount = post.heart_count ? parseInt(post.heart_count, 10) : 0;
      var wowCount = post.wow_count ? parseInt(post.wow_count, 10) : 0;

      var reactionHtml = '<span class="reaction-count" data-reaction="like">Likes: ' + likeCount + '</span>' +
             '<span class="reaction-count" data-reaction="heart"> Hearts: ' + heartCount + '</span>' +
             '<span class="reaction-count" data-reaction="wow"> Wows: ' + wowCount + '</span>';

      if (Array.isArray(post.comments) && post.comments.length > 0) {
        commentsHtml = '<ul class="list-group">' +
          post.comments.map(function(comment) {
            var commenter = comment.author_name ? escapeHtml(comment.author_name) : 'Unknown';
            var commentText = comment.comment ? escapeHtml(comment.comment) : '';
            var commentTime = comment.created_at ? escapeHtml(comment.created_at) : '';
            return '<li class="list-group-item"><strong>' + commenter + ':</strong> ' + commentText + ' <small class="text-muted">' + commentTime + '</small></li>';
          }).join('') +
          '</ul>';
      }

      var replySection = '';
      if (canReply && postId) {
        replySection = '<div class="reply-actions mt-3">' +
          '<button type="button" class="btn btn-sm btn-outline-primary reply-action" data-post-id="' + postId + '">Reply</button>' +
          '<div class="reply-form mt-2 d-none" id="reply-form-' + postId + '">' +
          '<form method="POST">' +
          '<input type="hidden" name="post_id" value="' + postId + '">' +
          '<input type="hidden" name="reply_to" value="' + postId + '">' +
          '<textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Write your reply..." required></textarea>' +
          '<button type="submit" class="btn btn-sm btn-primary mt-2">Send Reply</button>' +
          '</form>' +
          '</div>' +
          '</div>';
      } else if (canReply) {
        replySection = '<div class="reply-actions mt-3">' +
          '<p class="text-muted">Reply support is unavailable for this post.</p>' +
          '</div>';
      }

      var reactionButtons = '<div class="reaction-buttons">' +
        '<button class="btn btn-sm btn-outline-primary react-btn" data-post-id="' + postId + '" data-reaction="like">Like</button>' +
        '<button class="btn btn-sm btn-outline-danger react-btn" data-post-id="' + postId + '" data-reaction="heart">Heart</button>' +
        '<button class="btn btn-sm btn-outline-warning react-btn" data-post-id="' + postId + '" data-reaction="wow">Wow</button>' +
        '</div>';

      return '<div class="card mb-3"><div class="card-body">' +
        '<h5 class="card-title">' + employeeName + '</h5>' +
        '<p class="card-text">' + content + '</p>' +
        '<small class="text-muted">' + createdAt + '</small>' +
        '<div class="reactions mt-2">' + reactionHtml + '</div>' +
        reactionButtons +
        '<div class="comments mt-2">' + commentsHtml + '</div>' +
        replySection +
        '</div></div>';
    }).join('');

    setFeedHtml(html);
    updateAnalytics(posts);
  }

  function updateReactionCount(postId, reactionType, increment) {
    var postCard = document.querySelector('[data-post-id="' + postId + '"]').closest('.card');
    if (!postCard) return;
    var reactionCountSpan = postCard.querySelector('.reaction-count[data-reaction="' + reactionType + '"]');
    if (reactionCountSpan) {
      var currentCount = parseInt(reactionCountSpan.textContent, 10) || 0;
      reactionCountSpan.textContent = currentCount + (increment ? 1 : -1);
    }
  }

  socialFeed.addEventListener('click', function(event) {
    var button = event.target.closest('.react-btn');
    if (!button) return;

    var postId = button.getAttribute('data-post-id');
    var reactionType = button.getAttribute('data-reaction');

    var payload = { post_id: postId, type: reactionType };
    if (employeeId) {
      payload.employee_id = employeeId;
    }

    fetch('../api/reaction.php', {
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
          var action = data.result && data.result.status;
          if (action === 'added') {
            updateReactionCount(postId, reactionType, true);
          } else if (action === 'removed') {
            updateReactionCount(postId, reactionType, false);
          } else if (action === 'changed') {
            updateReactionCount(postId, data.result.old_type, false);
            updateReactionCount(postId, data.result.new_type, true);
          } else {
            updateReactionCount(postId, reactionType, true);
          }
        } else {
          alert(data.message || 'Failed to react.');
        }
      })
      .catch(function() {
        alert('Failed to send reaction.');
      });
  });

  function fetchSocialFeed() {
    fetch('../api/social.php?action=feed', {
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
          renderSocialFeed(data.data);
        } else {
          setFeedHtml('<p class="text-muted">No posts yet.</p>');
          updateAnalytics([]);
        }
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
    fetch('../api/group_member.php?action=list&group_id=' + encodeURIComponent(groupId), {
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

  var fileShareStatus = document.getElementById('file-share-status');
  var fileSharingForm = document.querySelector('.file-sharing-form');

  if (fileSharingForm) {
    fileSharingForm.addEventListener('submit', function(event) {
      event.preventDefault();

      if (!fileShareStatus) return;

      var fileInput = document.getElementById('file-upload');
      if (!fileInput || fileInput.files.length === 0) {
        fileShareStatus.innerHTML = '<div class="alert alert-danger">Please select a file to share.</div>';
        return;
      }

      var formData = new FormData();
      formData.append('shared_file', fileInput.files[0]);
      var description = document.getElementById('file-description').value;
      if (description) {
        formData.append('description', description);
      }

      fileShareStatus.innerHTML = '<div class="alert alert-info">Uploading...</div>';

      fetch('../api/file_sharing.php', {
        method: 'POST',
        body: formData
      })
        .then(function(response) {
          return response.json().then(function(data) {
            if (!response.ok) {
              throw new Error(data.message || 'Upload failed');
            }
            return data;
          });
        })
        .then(function(data) {
          if (data.success) {
            fileShareStatus.innerHTML = '<div class="alert alert-success">' + (data.message || 'File shared successfully.') + '</div>';
            fileInput.value = '';
            document.getElementById('file-description').value = '';
            fetchSharedFiles();
          } else {
            fileShareStatus.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Unable to share file.') + '</div>';
          }
        })
        .catch(function(err) {
          fileShareStatus.innerHTML = '<div class="alert alert-danger">' + (err.message || 'Failed to share file.') + '</div>';
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

      fetch('../api/group_member.php', {
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

  function fetchSharedFiles() {
    fetch('../api/shared_files.php?action=list', {
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
  fetchSharedFiles();
});
