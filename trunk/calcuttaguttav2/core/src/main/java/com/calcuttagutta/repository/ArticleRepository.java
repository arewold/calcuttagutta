package com.calcuttagutta.repository;

import java.util.List;

import com.calcuttagutta.model.Article;

public interface ArticleRepository {

	public abstract void saveArticle(Article article);

	public abstract Article getArticle(Integer articleId);

	public abstract List<Article> getAllArticles();

	public abstract void deleteArticle(Article article);

}