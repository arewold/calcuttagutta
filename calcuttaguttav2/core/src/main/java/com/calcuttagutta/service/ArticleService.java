package com.calcuttagutta.service;

import java.util.List;

import com.calcuttagutta.model.Article;

public interface ArticleService {

	public abstract void saveArticle(Article article);

	public abstract Article getArticle(Integer articleId);

	public abstract List<Article> getAllArticles();

	public abstract void deleteArticle(Article article);

}