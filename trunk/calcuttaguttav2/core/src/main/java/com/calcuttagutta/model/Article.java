package com.calcuttagutta.model;

import java.io.Serializable;
import java.util.Calendar;
import java.util.Date;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToOne;
import javax.persistence.PostLoad;
import javax.persistence.PrePersist;
import javax.persistence.Table;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import javax.persistence.Transient;

@Entity
@Table(name = "articles")
public class Article implements Serializable {

	private static final long serialVersionUID = 1L;

	@Id
	@GeneratedValue(strategy = GenerationType.AUTO)
	@Column(name = "articleid", nullable = false, unique = true)
	private Integer articleId;

	@Column(name = "title", nullable = false, length = 120)
	private String title;

	@Column(name = "author", nullable = false, length = 100)
	private String authorName;

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "author_username")
	private User author;

	@Column(name = "intro")
	private String intro;

	@Column(name = "body", nullable = false)
	private String body;

	@Temporal(TemporalType.DATE)
	@Column(name = "date_posted")
	private Date datePosted;

	@Temporal(TemporalType.TIME)
	@Column(name = "time_posted")
	private Date timePosted;

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "comment_to")
	private Article commentTo;

	@Transient
	private Date posted;

	/*
	 * Methods
	 */

	/**
	 * Merge the two date fields into one date after loading the object. The
	 * Posted variable is the only exposed value.
	 */
	@SuppressWarnings("unused")
	@PostLoad
	private void mergeDates() {
		if (datePosted != null && timePosted != null) {
			Calendar date = Calendar.getInstance();
			date.setTime(datePosted);
			
			Calendar time = Calendar.getInstance();
			time.setTime(timePosted);
			
			Calendar merged = Calendar.getInstance();
			merged.set(Calendar.YEAR, date.get(Calendar.YEAR));
			merged.set(Calendar.MONTH, date.get(Calendar.MONTH));
			merged.set(Calendar.DAY_OF_MONTH, date.get(Calendar.DAY_OF_MONTH));
			
			merged.set(Calendar.HOUR_OF_DAY, time.get(Calendar.HOUR_OF_DAY));
			merged.set(Calendar.MINUTE, time.get(Calendar.MINUTE));
			merged.set(Calendar.SECOND, time.get(Calendar.SECOND));
			merged.set(Calendar.MILLISECOND, time.get(Calendar.MILLISECOND));
			
			posted = merged.getTime();
		}
	}

	/**
	 * Split the two date fields before saving the object
	 */
	@SuppressWarnings("unused")
	@PrePersist
	private void splitDates() {
		if (posted != null) {
			Calendar calendar = Calendar.getInstance();
			
			calendar.clear();
			calendar.setTime(posted);
			calendar.clear(Calendar.HOUR_OF_DAY);
			calendar.clear(Calendar.MINUTE);
			calendar.clear(Calendar.SECOND);
			calendar.clear(Calendar.MILLISECOND);
			datePosted = calendar.getTime();

			calendar.clear();
			calendar.setTime(posted);
			calendar.clear(Calendar.YEAR);
			calendar.clear(Calendar.MONTH);
			calendar.clear(Calendar.DAY_OF_MONTH);
			timePosted = calendar.getTime();
		}
	}

	/*
	 * Getters and Setters
	 */
	public Integer getArticleId() {
		return articleId;
	}

	public void setArticleId(Integer articleId) {
		this.articleId = articleId;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public String getAuthorName() {
		return authorName;
	}

	public void setAuthorName(String authorName) {
		this.authorName = authorName;
	}

	public User getAuthor() {
		return author;
	}

	public void setAuthor(User author) {
		this.author = author;
	}

	public String getIntro() {
		return intro;
	}

	public void setIntro(String intro) {
		this.intro = intro;
	}

	public String getBody() {
		return body;
	}

	public void setBody(String body) {
		this.body = body;
	}

	public Date getPosted() {
		return posted;
	}

	public void setPosted(Date posted) {
		this.posted = posted;
	}

	public Article getCommentTo() {
		return commentTo;
	}

	public void setCommentTo(Article commentTo) {
		this.commentTo = commentTo;
	}

	public Integer getPriority() {
		return priority;
	}

	public void setPriority(Integer priority) {
		this.priority = priority;
	}

	public Integer getCategory() {
		return category;
	}

	public void setCategory(Integer category) {
		this.category = category;
	}

	public String getPictureUrl() {
		return pictureUrl;
	}

	public void setPictureUrl(String pictureUrl) {
		this.pictureUrl = pictureUrl;
	}

	public Boolean getDraft() {
		return draft;
	}

	public void setDraft(Boolean draft) {
		this.draft = draft;
	}

	public Boolean getDeleted() {
		return deleted;
	}

	public void setDeleted(Boolean deleted) {
		this.deleted = deleted;
	}

	public Integer getViewCount() {
		return viewCount;
	}

	public void setViewCount(Integer viewCount) {
		this.viewCount = viewCount;
	}

	@Deprecated
	@Column(name = "priority")
	private Integer priority;

	@Deprecated
	@Column(name = "category")
	private Integer category;

	@Deprecated
	@Column(name = "picture_url")
	private String pictureUrl;

	@Column(name = "is_draft")
	private Boolean draft;

	@Column(name = "is_deleted")
	private Boolean deleted;

	@Column(name = "view_count")
	private Integer viewCount;
}
