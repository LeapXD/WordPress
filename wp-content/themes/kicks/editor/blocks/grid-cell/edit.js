/* eslint-disable no-underscore-dangle */
/* eslint-disable no-nested-ternary */
/* eslint-disable no-case-declarations */
// File: blocks/grid-cell/edit.js
import {
  useBlockProps, useInnerBlocksProps, store as blockEditorStore, InspectorControls,
} from '@wordpress/block-editor';
import {
  ResizableBox, withFilters, PanelBody, RangeControl,
} from '@wordpress/components';
import { useRef, useMemo, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as postEditorStore } from '@wordpress/edit-post';
import { store as sitetEditorStore } from '@wordpress/edit-site';

import classnames from 'classnames';
import { compose } from '@wordpress/compose';

const { xd_settings: xdSettings } = window;
const { context: xdBlockContext } = xdSettings;
const { useXdBlockContext } = xdBlockContext;

function Edit({
  clientId,
  attributes,
  setAttributes,
  context,
  isSelected,
  className,
  innerBlocksSettings,
}) {
  const {
    colStart,
    colSpan,
    rowStart,
    rowSpan,
    gutters,
  } = attributes;

  const blockContext = useXdBlockContext();

  const { hasSelectedInnerBlock } = useSelect((select) => {
    const selectedClientId = select(blockEditorStore).getSelectedBlockClientId();
    const descendantClientIds = select(blockEditorStore).getClientIdsOfDescendants(clientId);
    return {
      hasSelectedInnerBlock: descendantClientIds.includes(selectedClientId),
    };
  });

  const columnCount = 24;

  const previewMode = useSelect((select) => (postEditorStore
    ? select(postEditorStore).__experimentalGetPreviewDeviceType()
    : sitetEditorStore
      ? select(sitetEditorStore).__experimentalGetPreviewDeviceType() : 'Desktop'), []).toLowerCase();

  const gridRows = context['xd/grid:rows'];

  const rows = gridRows[previewMode] || 1;
  const gutterSize = previewMode === 'mobile' ? 10 : 20;
  const parentEl = blockContext[clientId]?.contextRef?.current;
  const { showGridControls = false } = blockContext.state || {};

  const resizeRef = useRef(null);
  const ref = useRef(null);
  const parentWidth = parentEl?.clientWidth || 0;
  const gridColWidth = (parentWidth - 32) / columnCount;

  const xSnapPoints = useMemo(() => {
    const snapPoints = [0];
    for (let i = 1; i <= columnCount; i++) {
      const position = (i * gridColWidth) + ((gutterSize / columnCount) * i);
      const width = Math.round(position - gutterSize);
      snapPoints.push(width);
    }
    return snapPoints;
  }, [columnCount, gridColWidth, gutterSize]);

  const prevWidthRef = useRef(xSnapPoints[colSpan[previewMode]]);
  const prevRowCountDeltaRef = useRef(0);

  const [isResizing, setIsResizing] = useState(false);

  const handleResize = (event, handle, el, { width, height }) => {
    setIsResizing(true);
    const box = el.getBoundingClientRect();
    const colCount = xSnapPoints.indexOf(box.width);
    const prevColSpan = colSpan[previewMode] || 1;
    const prevColStart = colStart[previewMode] || 1;
    const colCountDelta = colCount - prevColSpan;

    const rowCountDelta = Math.floor(height / 32);
    const rowSpanDelta = rowCountDelta - prevRowCountDeltaRef.current;
    switch (handle) {
      case 'left': {
        const newColStart = prevColStart - colCountDelta;
        const newColSpan = prevColSpan + colCountDelta;
        if (width !== prevWidthRef.current && newColStart + newColSpan - 1 > columnCount) {
          return;
        }
        if (width !== prevWidthRef.current && newColSpan > -1) {
          setAttributes({
            colStart: {
              ...colStart,
              [previewMode]: newColStart,
            },
            colSpan: {
              ...colSpan,
              [previewMode]: newColSpan,
            },
          });
          prevWidthRef.current = width;
        }
        break;
      }
      case 'right': {
        const newColSpan = colCount;
        if (prevColStart + newColSpan - 1 > columnCount) {
          return;
        }
        setAttributes({
          colSpan: {
            ...colSpan,
            [previewMode]: newColSpan,
          },
        });
        break;
      }
      case 'top':

        if (rowSpan[previewMode] + rowSpanDelta < 1) {
          return;
        }

        if ((rowStart[previewMode] - rowSpanDelta) < 1) {
          return;
        }

        if (rowCountDelta !== prevRowCountDeltaRef.current) {
          setAttributes({
            rowStart: {
              ...rowStart,
              [previewMode]: rowStart[previewMode] - rowSpanDelta,
            },
            rowSpan: {
              ...rowSpan,
              [previewMode]: rowSpan[previewMode] + rowSpanDelta,
            },
          });
        }
        prevRowCountDeltaRef.current = rowCountDelta;
        break;
      case 'bottom':

        if (rowSpan[previewMode] + rowSpanDelta < 1) {
          return;
        }

        if (((rowStart[previewMode] + rowSpan[previewMode] - 1) + rowSpanDelta) > rows) {
          return;
        }

        if (rowCountDelta !== prevRowCountDeltaRef.current) {
          setAttributes({
            rowSpan: {
              ...rowSpan,
              [previewMode]: rowSpan[previewMode] + rowSpanDelta,
            },
          });
        }
        prevRowCountDeltaRef.current = rowCountDelta;
        break;
      default:
        break;
    }
  };

  const onResizeStop = () => {
    setIsResizing(false);
    prevWidthRef.current = 0;
    prevRowCountDeltaRef.current = 0;
  };

  const blockProps = useBlockProps({
    ref,
    className: classnames('grid-cell', {
      [`gutter-top-${gutters.top}`]: true,
      [`gutter-right-${gutters.right}`]: true,
      [`gutter-bottom-${gutters.bottom}`]: true,
      [`gutter-left-${gutters.left}`]: true,
      'is-resizing': isResizing,
    }, className),
    style: {
      border: 'none',
      position: 'relative',
      gridColumn: `${colStart[previewMode]} / span ${colSpan[previewMode]}`,
      gridRow: `${rowStart[previewMode]} / span ${rowSpan[previewMode]}`,
      zIndex: isSelected || hasSelectedInnerBlock ? 1 : 0,

    },
  });

  const { children, ...innerBlocksProps } = useInnerBlocksProps(blockProps, innerBlocksSettings);

  return (

    <>
      <InspectorControls>
        <PanelBody title="Grid Cell Settings" initialOpen>
          <RangeControl
            label="Column Start"
            value={colStart[previewMode]}
            onChange={(value) => {
              setAttributes({
                colStart: {
                  ...colStart,
                  [previewMode]: value,
                },
              });
            }}
            min={1}
            max={columnCount - colSpan[previewMode] + 1}
          />
          <RangeControl
            label="Column Span"
            value={colSpan[previewMode]}
            onChange={(value) => {
              setAttributes({
                colSpan: {
                  ...colSpan,
                  [previewMode]: value,
                },
              });
            }}
            min={1}
            max={columnCount - colStart[previewMode] + 1}
          />
          <RangeControl
            label="Row Start"
            value={rowStart[previewMode]}
            onChange={(value) => {
              setAttributes({
                rowStart: {
                  ...rowStart,
                  [previewMode]: value,
                },
              });
            }}
            min={1}
            max={rows}
          />
          <RangeControl
            label="Row Span"
            value={rowSpan[previewMode]}
            onChange={(value) => {
              setAttributes({
                rowSpan: {
                  ...rowSpan,
                  [previewMode]: value,
                },
              });
            }}
            min={1}
            max={rows - rowStart[previewMode] + 1}
          />
        </PanelBody>
      </InspectorControls>
      <div
        {...innerBlocksProps}
      >
        {(isSelected || hasSelectedInnerBlock || showGridControls) && (
        <ResizableBox
          ref={resizeRef}
          size={{ width: `${xSnapPoints[colSpan[previewMode]]}px`, height: 'auto' }}
          enable={{
            top: true,
            bottom: true,
            right: true,
            left: true,
          }}
          boundsByDirection
          style={{
            position: 'absolute',
            zIndex: 0,
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
          }}
          onResize={handleResize}
          onResizeStop={onResizeStop}
          snap={{ x: xSnapPoints }}
          className="grid-cell-resizable"
          minWidth={gridColWidth - gutterSize}
        />
        )}
        {!showGridControls && children}
      </div>
    </>
  );
}

export default compose(
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
)(Edit);
