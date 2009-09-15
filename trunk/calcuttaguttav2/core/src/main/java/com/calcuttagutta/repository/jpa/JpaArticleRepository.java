package com.calcuttagutta.repository.jpa;

import java.util.List;

import javax.persistence.EntityManager;
import javax.persistence.PersistenceContext;

import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

import com.calcuttagutta.model.Article;
import com.calcuttagutta.repository.ArticleRepository;

@Transactional(propagation=Propagation.REQUIRED, readOnly=true)
@Repository
public class JpaArticleRepository implements ArticleRepository {

	@PersistenceContext
	private EntityManager entityManager;
	
	/*
	 * Methods
	 */
	@Transactional(readOnly=false)
	public void saveArticle(Article article) {
		if (article.getArticleId() == null) {
			entityManager.persist(article);
		} else {
			entityManager.merge(article);
		}
	}

	public Article getArticle(Integer articleId) {
		return entityManager.find(Article.class, articleId);
	}

	@SuppressWarnings("unchecked")
	public List<Article> getAllArticles() {
		return entityManager.createQuery("select a from Article a").getResultList();
	}

	@Transactional(readOnly=false)
	public void deleteArticle(Article article) {
		entityManager.remove(article);
	}
}
