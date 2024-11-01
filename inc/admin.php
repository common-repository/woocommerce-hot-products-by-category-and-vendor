<div class="wrapper">
  <fieldset>
    <div class="option">
      <label for="title">
        <?php _e('Title', CWP_PLUGIN_WOO_HOT_SLUG); ?><br/>
      </label>
      <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="">
    </div>

    <div class="option">
      <label for="category_id">
        <?php _e('Category location', CWP_PLUGIN_WOO_HOT_SLUG); ?><br/>

      </label>
      <select id="<?php echo $this->get_field_id('category_id'); ?>" name="<?php echo $this->get_field_name('category_id'); ?>">
        <?php
          foreach($categories as $cat) {
            if ($cat->parent == 0) {
              $selected = '';
              if($instance['category_id'] == $cat->term_id) {
                $selected = ' selected="selected"';
              }
              echo '<option value="'.$cat->term_id.'"'.$selected.'>'.$cat->name.'</option>';
            }
          };
        ?>
      </select>
      <p><span class="description">i.e. Products that are the in the child categories of <strong>this parent</strong>.</span></p>
    </div>

    <div class="option">
      <label for="display_count">
        <?php _e('Maximum Best Sold', CWP_PLUGIN_WOO_HOT_SLUG); ?><br/>
      </label>
      <input type="text" id="<?php echo $this->get_field_id('display_count'); ?>" name="<?php echo $this->get_field_name('display_count'); ?>" value="<?php echo $instance['display_count']; ?>" class="">
    </div>

  </fieldset>
</div>