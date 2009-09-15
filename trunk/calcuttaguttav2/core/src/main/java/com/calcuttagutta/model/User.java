package com.calcuttagutta.model;

import java.awt.Image;
import java.io.Serializable;
import java.util.Date;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import javax.persistence.Transient;

@Entity
@Table(name = "user")
public class User implements Serializable {

	private static final long serialVersionUID = 1L;

	@Id
	@Column(name="username", nullable=false, length=16, unique=true)
	private String username;

	@Column(name="password", nullable=false, length=40)
	private String password;

	@Column(name="email", length=100)
	private String email;

	@Column(name="firstname", length=30)
	private String firstname;

	@Column(name="lastname", nullable=false, length=50)
	private String lastname;

	@Column(name="webpage", length=50)
	private String webpage;

	@Temporal(TemporalType.DATE)
	@Column(name="birthdate")
	private Date birthdate;

	@Column(name="description")
	private String description;

	@Column(name="admin")
	private Boolean admin;

	@Transient
	private Image picture;

	@Column(name="may_post")
	private Boolean mayPost;

	@Deprecated
	@Column(name="styleid")
	private Integer styleId;

	/*
	 * Getters and Setters
	 */
	public String getUsername() {
		return username;
	}

	public void setUsername(String username) {
		this.username = username;
	}

	public String getPassword() {
		return password;
	}

	public void setPassword(String password) {
		this.password = password;
	}

	public String getEmail() {
		return email;
	}

	public void setEmail(String email) {
		this.email = email;
	}

	public String getFirstname() {
		return firstname;
	}

	public void setFirstname(String firstname) {
		this.firstname = firstname;
	}

	public String getLastname() {
		return lastname;
	}

	public void setLastname(String lastname) {
		this.lastname = lastname;
	}

	public String getName() {
		return getFirstname() + " " + getLastname();
	}
	
	public String getWebpage() {
		return webpage;
	}

	public void setWebpage(String webpage) {
		this.webpage = webpage;
	}

	public Date getBirthdate() {
		return birthdate;
	}

	public void setBirthdate(Date birthdate) {
		this.birthdate = birthdate;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public Boolean isAdmin() {
		return admin;
	}

	public void setAdmin(Boolean admin) {
		this.admin = admin;
	}

	public Image getPicture() {
		return picture;
	}

	public void setPicture(Image picture) {
		this.picture = picture;
	}

	public Boolean isMayPost() {
		return mayPost;
	}

	public void setMayPost(Boolean mayPost) {
		this.mayPost = mayPost;
	}

	public Integer getStyleId() {
		return styleId;
	}

	public void setStyleId(Integer styleId) {
		this.styleId = styleId;
	}
}
