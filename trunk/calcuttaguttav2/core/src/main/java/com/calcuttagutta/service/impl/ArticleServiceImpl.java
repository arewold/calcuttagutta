package com.calcuttagutta.service.impl;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.calcuttagutta.model.Article;
import com.calcuttagutta.repository.ArticleRepository;
import com.calcuttagutta.service.ArticleService;

@Service
public class ArticleServiceImpl implements ArticleService {

	@Autowired
	private ArticleRepository articleRepository;
	
	public void saveArticle(Article article) {
		articleRepository.saveArticle(article);
	}

	public Article getArticle(Integer articleId) {
		return articleRepository.getArticle(articleId);
	}

	public List<Article> getAllArticles() {
		return articleRepository.getAllArticles();
	}

	public void deleteArticle(Article article) {
		articleRepository.deleteArticle(article);
	}
}
